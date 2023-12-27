@extends('layouts.app')

@section('content')
    <div class="container pt-4 ">
        <div class="tableDiv col-md-10 mx-auto">
            <h1 class="text-center">
                Create News!
            </h1>
            <div id="successMessage" class="alert alert-success d-none">
                News post created successfully.
            </div>
            <div id="failureMessage" class="alert alert-danger d-none">
                The news was not created. Please try again.
            </div>
            <input class="newsTitle" type="text" placeholder="Title" aria-label="Title" id="news_title" name="news_title">
            <div id="editorjs" class="border" style="background-color: white; min-height: 50vh; overflow-y: auto;">
            </div>
            <div class="text-center">
                Unsure on how to operate the editor? Check out the <a href="https://editorjs.io/_nuxt/editor-1.a97c8c4a.jpg"
                    target="_blank">documentation</a>.
            </div>
            <div class="text-center"><a id="save-data" href="{{ route('editorjsJsonUpload') }}"
                    data-image_upload="{{ route('editorjsImageUpload') }}" class="btn btn-primary">Submit</a></div>
        </div>
    </div>
    @vite(['resources/js/codex-editor.js'])
    <script></script>
@endsection
