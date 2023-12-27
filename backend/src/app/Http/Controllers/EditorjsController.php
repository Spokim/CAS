<?php

namespace App\Http\Controllers;

use App\Models\News_post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class EditorjsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function linkToolUpload(Request $request)
    {
        $url = $request->url;
        if (!$url) return response()->json(['error' => 'No url provided.'], 400);

        $response = Http::get($url);
        $data = [
            'success' => 1,
            'link' => $url,
            'meta' => [],
        ];

        if ($response->successful()) {
            $content = $response->body();

            $titleMatch = preg_match('/<title>(.*?)<\/title>/', $content, $title);
            $descriptionMatch = preg_match('/<meta name="description" content="(.*?)"/', $content, $description);
            $imageMatch = preg_match('/<meta property="og:image" content="(.*?)"/', $content, $imageUrl);

            $decode = html_entity_decode($description[1], ENT_COMPAT, 'UTF-8');

            $image_url = $imageMatch ? $imageUrl[1] : '';
            $image_url = str_replace('\/', '/', $image_url);

            $data['meta']['title'] = $titleMatch ? $title[1] : '';
            $data['meta']['description'] = $descriptionMatch ? $decode : '';
            $data['meta']['image_url'] = $imageMatch ? $image_url : '';

        } else {
            $data['success'] = 0;
            $data['error'] = 'Failed to fetch URL content';
        }

        return response()->json($data, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function editorjsImageUpload(Request $request) {

        $file = $request->image;

        $fileNameWithExtension = $file->getClientOriginalName();

        $file_name = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);

        $file_extension = $file->getClientOriginalExtension();

        $file_name_to_store = $file_name . '_' . time() . '.' . $file_extension;

        $file->storeAs('public', $file_name_to_store);

        $image_preview_path = "storage/" . $file_name_to_store;

        $res = array(
            'success' => 1,
            'file' => (object) array(
                'url' => $image_preview_path,
            )
        );
        return response()->json($res);
    }

    public function editorjsJsonUpload(Request $request) {

        $request->validate([
            'title' => 'required',
        ]);

        try {
            DB::transaction(function() use ($request) {
                $news = new News_post();
                $news->title = $request->title;
                $news->content = json_encode($request->content['blocks']);
                $news->user_id = Auth::id();
                $news->save();
            });
            return response()->json(['success' => 'News post created successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create news post. Reason: ' . $e->getMessage()], 400);
        }
    }
}
