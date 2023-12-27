@extends('layouts.app')

@section('content')
    <div class="container pt-4">
        <div class="tableDiv">
            <div class="text-center">
                <h1 class="newsTitle pt-3">Home</h1>
            </div>
            <div class="col-10 mx-auto">
                <p>Welcome back <strong>{{ Auth::user()->name }}</strong>!</p>
                <p>You last posted your work shift on: <strong class="text-danger">{{ $lastShift->work_date ?? 'Never reported a shift' }}</strong></p>
            </div>
        </div>
        <div class="tableDiv my-4 xxx">
            <div class="col-10 mx-auto pt-4">
                <h2 class="newsTitle text-center">News</h2>
                <div id="newsBlocks">
                    @foreach ($news as $index => $newsItem)
                        <div class="bg-white shadow my-4">
                            <div class="px-4 py-2">
                                <div class="mt-1 small text-muted">
                                    <span>{{ 'Posted: ' . $newsItem->created_at }}</span>
                                </div>
                                <h1 class="py-2" id="postTitle">
                                    {{ $newsItem->title }}</h1>
                                <div>
                                    @php
                                        $blocks = json_decode($newsItem->content);
                                        foreach ($blocks as $block) {
                                            if ($block->type === 'paragraph') {
                                                echo '<p class="">' . $block->data->text . '</p>';
                                            }
                                            if ($block->type === 'header') {
                                                echo '<h' . $block->data->level . ' ' . 'class="">' . $block->data->text . '</h' . $block->data->level . '>';
                                            }
                                            if ($block->type === 'table') {
                                                echo '<table class="table table-bordered table-striped">';
                                                foreach ($block->data->content as $row) {
                                                    echo '<tr>';
                                                    foreach ($row as $cell) {
                                                        echo '<td style="padding: 8px; border: 1px solid #ddd;">' . $cell . '</td>';
                                                    }
                                                    echo '</tr>';
                                                }
                                                echo '</table>';
                                            }
                                            if ($block->type === 'list') {
                                                echo '<ul class="list-disc">';
                                                foreach ($block->data->items as $item) {
                                                    echo '<li>' . $item . '</li>';
                                                }
                                                echo '</ul>';
                                            }
                                            if ($block->type === 'image') {
                                                $divStyles = 'max-width: 100%; margin-bottom: 1rem;'; // Default style for all images
                                                $imgStyles = 'max-width: 100%;';

                                                if ($block->data->stretched === true) {
                                                    // If stretched is true, set the width to 100%
                                                    $divStyles .= 'width: 100%;';
                                                    $imgStyles .= 'width: 100%;';
                                                }

                                                // Check for other options and add corresponding styles
                                                if ($block->data->withBorder === true) {
                                                    $divStyles .= 'border: 1px solid #e8e8eb;';
                                                }

                                                if ($block->data->withBackground === true) {
                                                    $divStyles .= 'background-color: #cdd1e0;';
                                                    $divStyles .= 'text-align: center;';
                                                    $imgStyles .= 'padding: 15px;';
                                                    $imgStyles .= 'max-width: 60%;';
                                                }

                                                echo '<div style="' . $divStyles . '"><img src="' . asset($block->data->file->url) . '" alt="' . $block->data->caption . '" style="' . $imgStyles . '"></div>';
                                            }
                                            if ($block->type === 'linkTool') {
                                                echo '<a href="' . $block->data->link . '" target="_blank" rel="noopener noreferrer">' . $block->data->link . '</a>';
                                            }
                                        }
                                    @endphp
                                </div>
                            </div>
                        </div>
                        @if ($index === 1)
                            <div id="load-more-placeholder"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        let nextPageUrl = '{{ $news->nextPageUrl() }}';

        function loadMoreNews() {
            if (!nextPageUrl) return;
            $.ajax({
                url: nextPageUrl,
                type: 'get',
                dataType: 'json',
                success: function(response) {
                    // Create a temporary div to hold the response
                    var tempDiv = document.createElement('div');
                    tempDiv.innerHTML = response.view;

                    // Get the app div from the response
                    var appDiv = tempDiv.querySelector('#app');

                    // Get the target div from the app div
                    var target = appDiv.querySelector('#newsBlocks');
                    var children = target.children

                    var newsBlocks = document.getElementById('newsBlocks');

                    // Add the children to the newsBlocks div
                    for (var i = 0; i < children.length; i++) {
                        newsBlocks.appendChild(children[i].cloneNode(true));
                    }
                    nextPageUrl = response.nextPageUrl;
                    if (!nextPageUrl) {
                        $('#load-more-placeholder').remove();
                    } else {
                        startObserve();
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + xhr.responseJSON.error);
                },
            });
        }

        function startObserve() {
            const target = document.getElementById('load-more-placeholder');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        observer.unobserve(target);
                        $('#load-more-placeholder').remove();
                        loadMoreNews();
                    }
                });
            });
            observer.observe(target);
        }

        startObserve();
    </script>
@endsection
