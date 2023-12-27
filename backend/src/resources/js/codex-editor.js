import EditorJS from "@editorjs/editorjs";
import Header from "@editorjs/header";
import List from "@editorjs/list";
import Table from "@editorjs/table";
import LinkTool from "@editorjs/link";
import axios from "axios";
import ImageTool from "@editorjs/image";


let saveButton = document.getElementById("save-data");
let titleInput = document.getElementById("news_title");

let failureDiv = document.getElementById("failureMessage");
let successDiv = document.getElementById("successMessage");

let imageUploadUrl = saveButton.dataset.image_upload;
let meta = document.head.querySelector('meta[name="csrf-token"]');

const editor = new EditorJS({
    /**
     * Id of Element that should contain Editor instance
     */
    holder: "editorjs",
    minHeight: 500,
    placeholder: "Let`s create News!",
    inlineToolbar: true,
    /**
     * Available Tools list.
     */
    tools: {
        header: Header,
        list: List,
        table: Table,
        linkTool: {
            class: LinkTool,
            config: {
                endpoint: "/linkTool-upload",
            },
        },
        image: {
            class: ImageTool,
            config: {
                endpoints: {
                    byFile: `${imageUploadUrl}`, // Your backend file uploader endpoint
                    byUrl: `${imageUploadUrl}`, // Your endpoint that provides uploading by Url
                },
                
                additionalRequestHeaders: {
                    "X-CSRF-TOKEN": meta.content,
                },
            },
        },
    },
    autofocus: true,
});
let saving = false;

if (saveButton) {
    saveButton.addEventListener("click", (e) => {
        e.preventDefault();
        if (saving) return;
        if (successDiv.classList.contains("d-none") === false) successDiv.classList.add("d-none");
        if (failureDiv.classList.contains("d-none") === false) failureDiv.classList.add("d-none");
        let aTag = e.target;
        const url = aTag.getAttribute("href");
        const title = titleInput.value;
        $('#save-data')
            .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...')
        editor
            .save()
            .then((outputData) => {
                saving = true;
                axios({
                    method: "post",
                    url: url,
                    data: {
                        title: title,
                        content: outputData,
                    },
                })
                .then((response) => {
                    if (response.data.success === "News post created successfully.") {
                        // Show the success message and hide the failure message
                        successDiv.classList.remove("d-none");
                        failureDiv.classList.add("d-none");
                        document.getElementById("news_title").value = "";
                        editor.clear();
                    } else {
                        // Show the failure message and hide the success message
                        failureDiv.classList.remove("d-none");
                        successDiv.classList.add("d-none");
                    }
                    saving = false;
                    $('#save-data').html('Submit');
                })
                .catch((error) => {
                    failureDiv.classList.remove("d-none");
                    console.log("Saving failed: ", error);
                    saving = false;
                    $('#save-data').html('Submit');
                });
            })
            .catch((error) => {
                failureDiv.classList.remove("d-none");
                console.log("Saving failed: ", error);
            });
        });
}
