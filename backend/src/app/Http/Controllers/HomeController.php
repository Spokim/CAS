<?php

namespace App\Http\Controllers;

use App\Models\Authorized_users;
use App\Models\News_post;
use App\Models\User;
use App\Models\Work_shift;
use App\Rules\AfterStartTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function home(Request $request)
    {
        $news = News_post::orderBy('created_at', 'desc')->paginate(3);
        $lastShift = Work_shift::where('user_id', Auth::id())->orderBy('work_date', 'desc')->first();

        if ($request->ajax()) {
            $view = view('home', compact('news', 'lastShift'))->render();

            return response()->json(['view' => $view, 'nextPageUrl' => $news->nextPageUrl(), 'lastShift' => $lastShift]);
        }
        return view('home', [
            'news' => $news,
            'lastShift' => $lastShift,
        ]);
    }

    public function workShift()
    {
        return view('components.work-shift.work-shift');
    }

    public function pastWorkShift()
    {
        return view('components.past-work-shift.past-work-shift');
    }

    public function getPastWorkShift(Request $request)
    {
        $request->validate([
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => [
                'required',
                'date_format:Y-m-d',
                function ($attribute, $value, $fail) use ($request) {
                    if (strtotime($value) < strtotime($request->start_date)) {
                        $fail($attribute . ' must be later than or equal to start date.');
                    }
                },
            ],
        ]);

        $workShifts = Work_shift::where('user_id', Auth::id())
            ->where('work_date', '>=', $request->start_date)
            ->where('work_date', '<=', $request->end_date)
            ->orderBy('work_date', 'desc')
            ->get();
        return response()->json(['workShifts' => $workShifts]);
    }

    public function createNews()
    {
        return view('create-news');
    }

    public function supervisor(Request $request)
    {
        $employees = User::where(function ($query) {
            $query->where('register_privileges', '!=', 1)
                ->where('admin_privileges', '!=', 1);
        })
            ->orWhere('id', Auth::id())
            ->paginate(10);

        if ($request->ajax()) {
            $action = $request->action;
            $id = $request->id;
            $view = view('components.supervisor.load', compact('employees'))->render();

            return Response::json(['view' => $view, 'nextPageUrl' => $employees->nextPageUrl()]);
        }

        return view('components.supervisor.supervisor', [
            'employees' => $employees,
        ]);
    }

    public function getWorkShifts(Request $request)
    {
        // Check if the user is a supervisor or admin.
        try {
            $id = $request->id;
            $user = User::find($id);
            if (($user->register_privileges == 1 || $user->admin_privileges == 1) && $user->id != Auth::id()) {
                throw new \Exception('Unauthorized action! You are not allowed to view this user\'s work shifts');
            }
            $workShifts = Work_shift::where('user_id', $id)->orderBy('work_date', 'desc')->paginate(10);

            return $workShifts;
        } catch (\Exception $e) {
            return redirect(route('supervisor'))->with(
                'error',
                'Failed to get work shifts. Reason:' . "\n" . $e->getMessage(),
            );
        }
    }

    public function admin()
    {
        return view('admin');
    }

    public function postWorkData(Request $request)
    {
        $request->validate([
            'date' => ['required', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
            'start_time' => ['required', 'regex:/^(2[0-3]|[01][0-9]):([0-5][0-9])$/'],
            'end_time' => ['required', 'regex:/^(2[0-3]|[01][0-9]):([0-5][0-9])$/', new AfterStartTime],
        ]);

        try {
            // Check if the user already has a work shift on the same timespan.
            $userHasWorkDuringTimespan = Work_shift::where('user_id', Auth::id())
                ->where('work_date', $request->date)
                ->where(function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->start_time);
                    })
                        ->orWhere(function ($query) use ($request) {
                            $query->where('start_time', '<=', $request->overtime_end_time ?? $request->end_time)
                                ->where('end_time', '>=', $request->overtime_end_time ?? $request->end_time);
                        })
                        ->orWhere(function ($query) use ($request) {
                            $query->where('start_time', '>=', $request->start_time)
                                ->where('end_time', '<=', $request->overtime_end_time ?? $request->end_time);
                        });
                })
                ->first();
            if ($userHasWorkDuringTimespan) {
                throw new \Exception('A work shift already exists during the specified time range.');
            }

            DB::transaction(function () use ($request) {
                function timeToMinutes($time)
                {
                    list($hours, $minutes) = explode(':', $time);
                    return intval($hours) * 60 + intval($minutes);
                }
                $normalWorkMinutes = 0;
                if (timeToMinutes($request->start_time) > timeToMinutes($request->end_time)) {
                    $normalWorkMinutes = 24 * 60 - (timeToMinutes($request->start_time) + timeToMinutes($request->end_time));
                } else {
                    $normalWorkMinutes = timeToMinutes($request->end_time) - timeToMinutes($request->start_time);
                }
                $overtimeWorkMinutes = 0;
                if ($request->overtime_start_time) {
                    if (timeToMinutes($request->overtime_start_time) > timeToMinutes($request->overtime_end_time)) {
                        $overtimeWorkMinutes = 24 * 60 - (timeToMinutes($request->overtime_start_time) + timeToMinutes($request->overtime_end_time));
                    } else {
                        $overtimeWorkMinutes = timeToMinutes($request->overtime_end_time) - timeToMinutes($request->overtime_start_time);
                    }
                }

                $workShift = new Work_shift();
                $workShift->user_id = Auth::id();
                $workShift->work_date = $request->date;
                $workShift->start_time = $request->start_time;
                $workShift->end_time = $request->overtime_end_time ?? $request->end_time;
                $workShift->work_minutes = $normalWorkMinutes;
                $workShift->overtime_minutes = $overtimeWorkMinutes;
                $workShift->save();
            });

            return redirect(route('work-shift'))->with(
                'success',
                'Successfully created work shift.',
            );
        } catch (\Exception $e) {
            return redirect(route('work-shift'))->with(
                'error',
                'Failed to create work shift. Reason:' . "\n" . $e->getMessage(),
            );
        }
    }

    public function grantPrivileges(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        try {
            $newUser = Authorized_users::where('email', $request->email)->first();
            if ($newUser) {
                throw new \Exception('User already has login privileges.');
            }
            $authorizedUser = new Authorized_users();
            $authorizedUser->email = $request->email;
            $authorizedUser->user_id = Auth::id();
            $authorizedUser->save();

            return response()->json(['success' => 'Successfully granted login privileges.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to grant login privileges. Reason: ' . $e->getMessage()], 400);
        }
    }
    public function revokePrivileges(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        try {
            $priviligedUser = User::where('email', $request->email)
                ->where(function ($query) {
                    $query->where('register_privileges', 1)
                        ->orWhere('admin_privileges', 1);
                })
                ->first();
            if ($priviligedUser) {
                throw new \Exception('User is a supervisor or admin. Cannot revoke login privileges.');
            }

            $newUser = Authorized_users::where('email', $request->email)->first();
            if (!$newUser) {
                throw new \Exception('User does not have login privileges.');
            }

            $newUser->delete();

            return response()->json(['success' => 'Successfully revoked login privileges.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to revoke login privileges. Reason: ' . $e->getMessage()], 400);
        }
    }

    public function grantSupervisorPrivileges(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                throw new \Exception('User does not exist.');
            }
            if ($user->register_privileges == 1) {
                return throw new \Exception('User already has supervisor privileges.');
            }
            if ($user->admin_privileges == 1) {
                return throw new \Exception('User is an admin. Cannot grant supervisor privileges.');
            }
            $user->register_privileges = 1;
            $user->save();
            return response()->json(['success' => 'Successfully granted supervisor privileges.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to grant supervisor privileges. Reason: ' . $e->getMessage()], 400);
        }
    }
    public function revokeSupervisorPrivileges(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                throw new \Exception('User does not exist.');
            }
            if ($user->admin_privileges == 1) {
                return throw new \Exception('User is an admin. Cannot revoke supervisor privileges.');
            }
            if ($user->register_privileges == 0) {
                return throw new \Exception('User does not have supervisor privileges.');
            }
            $user->register_privileges = 0;
            $user->save();
            return response()->json(['success' => 'Successfully revoked supervisor privileges.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to revoke supervisor privileges. Reason: ' . $e->getMessage()], 400);
        }
    }
}
