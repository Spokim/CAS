const startTimeInput = document.getElementById("startTime");
const endTimeInput = document.getElementById("endTime");
const overtimeStartTimeInput = document.getElementById("overtimeStartTime");
const overtimeEndTimeInput = document.getElementById("overtimeEndTime");
const dateInput = document.getElementById("date");

function insertSemiColon(inputField) {
    let previousValue = "";

    return function (e) {
        let time = e.target.value;

        if (previousValue.endsWith(":") && time.length === 2) {
            time = time.slice(0, 1);
        } else if (time.length === 2 && !time.includes(":")) {
            time += ":";
        } else if (time.length === 3 && time.includes(":")) {
            time = time.slice(0, -1);
        } else if (time.length === 3 && !time.includes(":")) {
            time = time.slice(0, 2) + ":" + time.slice(2);
        }

        previousValue = time;
        e.target.value = time;
    };
}

startTimeInput.addEventListener("input", insertSemiColon("startTime"));
endTimeInput.addEventListener("input", insertSemiColon("endTime"));
overtimeStartTimeInput.addEventListener(
    "input",
    insertSemiColon("overtimeStartTime")
);
overtimeEndTimeInput.addEventListener(
    "input",
    insertSemiColon("overtimeEndTime")
);

const overtimeCheckbox = document.getElementById("overtimeCheckbox");
const overtimeFields = document.getElementById("overtimeFields");

overtimeCheckbox.addEventListener("change", function () {
    if (this.checked) {
        overtimeFields.classList.remove("d-none");
        document.getElementById("overtimeStartTime").required = true;
        document.getElementById("overtimeEndTime").required = true;
    } else {
        overtimeFields.classList.add("d-none");
        document.getElementById("overtimeStartTime").required = false;
        document.getElementById("overtimeEndTime").required = false;
        // Clear the overtime input fields
        document.getElementById("overtimeStartTime").value = "";
        document.getElementById("overtimeEndTime").value = "";
    }
});

const form = document.getElementById("workShiftForm");

function isValidTimeFormat(time) {
    //Match for HH:MM where HH is 00-23 and MM is 00-59
    const timeRegex = /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;
    return timeRegex.test(time);
}

form.addEventListener("submit", function (event) {
    let validated = true;
    // Remove any previous validation errors
    startTimeInput.classList.remove("is-invalid");
    endTimeInput.classList.remove("is-invalid");
    overtimeStartTimeInput.classList.remove("is-invalid");
    overtimeEndTimeInput.classList.remove("is-invalid");
    dateInput.classList.remove("is-invalid");

    if (!isValidTimeFormat(startTimeInput.value)) {
        startTimeInput.classList.add("is-invalid");
        validated = false;
        event.preventDefault();
    }

    if (!isValidTimeFormat(endTimeInput.value)) {
        endTimeInput.classList.add("is-invalid");
        validated = false;
        event.preventDefault();
    }

    if (
        !isValidTimeFormat(overtimeStartTimeInput.value) &&
        !overtimeStartTimeInput.classList.contains("d-none") &&
        overtimeStartTimeInput.required
    ) {
        overtimeStartTimeInput.classList.add("is-invalid");
        validated = false;
        event.preventDefault();
    }

    if (
        !isValidTimeFormat(overtimeEndTimeInput.value) &&
        !overtimeEndTimeInput.classList.contains("d-none") &&
        overtimeEndTimeInput.required
    ) {
        overtimeEndTimeInput.classList.add("is-invalid");
        validated = false;
        event.preventDefault();
    }

    // Check if overtime start time is before end time
    function timeToMinutes(time) {
        const [hours, minutes] = time.split(":");
        return parseInt(hours) * 60 + parseInt(minutes);
    }

    if (
        timeToMinutes(overtimeStartTimeInput.value) <
        timeToMinutes(endTimeInput.value)
    ) {
        overtimeStartTimeInput.classList.add("is-invalid");
        overtimeEndTimeInput.classList.add("is-invalid");
        validated = false;
        event.preventDefault();
    }

    if ($('#date').val() == '') {
        validated = false;
        dateInput.classList.add("is-invalid");
        event.preventDefault();
    }
    if (validated) {
        var element = $("#submitButtom");
        element.prop("disabled", true);
        element.html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
        );
        form.classList.add("was-validated");
    }
});
