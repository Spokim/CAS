const userPrivileges = document.getElementById("user-email");
const loginPrivileges = (action) => {
    if (!userPrivileges.value) {
        alert("Please enter a valid email address");
        return;
    }
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (action === "Grant") {
        ElementLoad("#grantLoginPrivileges");
        $.ajax({
            url: grantPrivilegesRoute,
            type: "post",
            headers: {
                "X-CSRF-TOKEN": meta.content,
            },
            data: {
                email: userPrivileges.value,
            },
            success: function (response) {
                userPrivileges.value = "";
                alert(response.success);
                ElementLoadReset(
                    "#grantLoginPrivileges",
                    "Grant Login Privileges"
                );
            },
            error: function (xhr, status, error) {
                alert("Error: " + xhr.responseJSON.error);
                ElementLoadReset(
                    "#grantLoginPrivileges",
                    "Grant Login Privileges"
                );
            },
        });
    }
    if (action === "Revoke") {
        ElementLoad("#revokeLoginPrivileges");
        $.ajax({
            url: revokePrivilegesRoute,
            type: "post",
            headers: {
                "X-CSRF-TOKEN": meta.content,
            },
            data: {
                email: userPrivileges.value,
            },
        })
            .then((response) => {
                userPrivileges.value = "";
                alert(response.success);
                ElementLoadReset(
                    "#revokeLoginPrivileges",
                    "Revoke Login Privileges"
                );
            })
            .catch((error) => {
                alert("Error: " + error.responseJSON.error);
                ElementLoadReset(
                    "#revokeLoginPrivileges",
                    "Revoke Login Privileges"
                );
            });
    }
};

let loading = false;

function loadMoreEmployees() {
    if (loading || !nextPageUrl) return;

    $.ajax({
        url: nextPageUrl,
        type: "get",
        dataType: "json",
        success: function (response) {
            var parsedresponse = $.parseHTML(response.view);

            var addEmployees = document.getElementById("addEmployees");

            for (let i = 0; i < parsedresponse.length; i++) {
                if (parsedresponse[i].tagName === "TR") {
                    addEmployees.appendChild(parsedresponse[i]);
                }
            }

            nextPageUrl = response.nextPageUrl;
            $(".fetch-loading").remove();
            loading = false;
        },
        error: function (xhr, status, error) {
            console.error("Error loading more entries: ", error);
            $(".fetch-loading").remove();
            loading = false;
        },
        beforeSend: function () {
            loading = true;
            $(".employeeTable").append(
                '<tr class="fetch-loading"><td colspan="4" class="text-center">Loading...<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>'
            );
        },
    });
}

$(".xxx").on("scroll", function () {
    if (!loading && nextPageUrl) {
        let tableDiv = $(".xxx")[0].getBoundingClientRect();
        let loadMoreData =
            $(".load-more-placeholder")[0].getBoundingClientRect() ?? null;

        // Check if the placeholder element is in or near the viewport
        if (tableDiv.bottom >= loadMoreData.bottom) {
            $(".load-more-placeholder").remove();
            loadMoreEmployees();
        }
    }
});

// help variables for infinite scrolling
var next_page_url = null;
var id = null;
var loading1 = false;

// helper function to add No more records to the table
function addNoMoreRecords(tbody) {
    $("#workshiftDataInput").append(
        '<tr><td colspan="6" class="text-center">No more records</td></tr>'
    );
}

function LoadWorkShift(employeeId) {
    id = employeeId;
    // check if the same employee is clicked
    if ($("." + employeeId)[0]) return;
    // if not, empty the table
    if (!$("." + employeeId)[0]) $("#workshiftDataInput").empty();

    $.ajax({
        url: trialURL,
        type: "get",
        data: {
            id: employeeId,
        },
        success: function (response) {
            var parsedresponse = response.data;
            next_page_url = response.next_page_url;
            if (!next_page_url && parsedresponse.length < 1) addNoMoreRecords();

            for (let i = 0; i < parsedresponse.length; i++) {
                $("#workshiftDataInput").append(
                    "<tr class=" +
                        employeeId +
                        '><th scope="row">' +
                        parsedresponse[i].user_id +
                        "</th><td>" +
                        parsedresponse[i].work_date +
                        "</td><td>" +
                        parsedresponse[i].start_time +
                        "</td><td>" +
                        parsedresponse[i].end_time +
                        "</td><td>" +
                        parsedresponse[i].work_minutes +
                        "</td><td>" +
                        parsedresponse[i].overtime_minutes +
                        "</td></tr>"
                );
                if (i === 7) {
                    $("#workshiftDataInput").append(
                        '<tr class="load-more-work-shifts-placeholder"></tr>'
                    );
                }
                if (i === parsedresponse.length - 1 && !next_page_url) {
                    addNoMoreRecords();
                }
            }
            $(".fetch-loading").remove();
        },
        error: function (xhr, status, error) {
            $(".fetch-loading").remove();
            console.error("Error loading more entries: ", error);
        },
        beforeSend: function () {
            $("#workshiftDataInput").append(
                '<tr class="fetch-loading"><td colspan="6" class="text-center">Loading...<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>'
            );
        },
    });
}

function infiniteWorkShifts() {
    if (loading1 || !next_page_url) return;
    $.ajax({
        url: next_page_url,
        type: "get",
        data: {
            id: id,
        },
        success: function (response) {
            var parsedresponse = response.data;
            next_page_url = response.next_page_url;
            for (let i = 0; i < parsedresponse.length; i++) {
                $("#workshiftDataInput").append(
                    "<tr class=" +
                        id +
                        '><th scope="row">' +
                        parsedresponse[i].user_id +
                        "</th><td>" +
                        parsedresponse[i].work_date +
                        "</td><td>" +
                        parsedresponse[i].start_time +
                        "</td><td>" +
                        parsedresponse[i].end_time +
                        "</td><td>" +
                        parsedresponse[i].work_minutes +
                        "</td><td>" +
                        parsedresponse[i].overtime_minutes +
                        "</td></tr>"
                );
                if (i === 7 && next_page_url) {
                    $("#workshiftDataInput").append(
                        '<tr class="load-more-work-shifts-placeholder"></tr>'
                    );
                }
                if (i === parsedresponse.length - 1 && !next_page_url) {
                    addNoMoreRecords();
                }
            }
            $(".fetch-loading").remove();
            loading1 = false;
        },
        error: function (xhr, status, error) {
            console.error("Error loading more entries: ", error);
            $(".fetch-loading").remove();
            loading1 = false;
        },
        beforeSend: function () {
            loading1 = true;
            $(".workshiftTable").append(
                '<tr class="fetch-loading"><td colspan="6" class="text-center">Loading...<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>'
            );
        },
    });
}
$(".zzz").on("scroll", function () {
    if (!loading1 && next_page_url) {
        let tableDiv = $(".zzz")[0].getBoundingClientRect();
        let loadMoreData = $(
            ".load-more-work-shifts-placeholder"
        )[0].getBoundingClientRect();

        // Check if the placeholder element is in or near the viewport
        if (tableDiv.bottom >= loadMoreData.bottom) {
            $(".load-more-work-shifts-placeholder").remove();
            infiniteWorkShifts();
        }
    }
});

const ElementLoad = (id) => {
    var element = $(id);
    element.prop("disabled", true);
    element.html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
    );
};

const ElementLoadReset = (id, text) => {
    var element = $(id);
    element.prop("disabled", false);
    element.html(text);
};
