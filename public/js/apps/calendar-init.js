document.addEventListener("DOMContentLoaded", function () {

    //  Calender Date variable
    var newDate = new Date();

    function getDynamicMonth() {
        getMonthValue = newDate.getMonth();
        _getUpdatedMonthValue = getMonthValue + 1;
        if (_getUpdatedMonthValue < 10) {
            return `0${_getUpdatedMonthValue}`;
        } else {
            return `${_getUpdatedMonthValue}`;
        }
    }

    function getDynamicDay() {
        const getDayValue = newDate.getDate(); 
        if (getDayValue < 10) {
            return `0${getDayValue}`; 
        } else {
            return `${getDayValue}`; 
        }
    }

    function formatDateTime(date) {
        const pad = num => num.toString().padStart(2, '0');
        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
    }

    // Calender Modal Elements
    var getModalTitleEl = document.querySelector("#schedule-title");
    var getModalDescriptionEl = document.querySelector("#schedule-description");
    var getModalStartDateEl = document.querySelector("#schedule-start-date");
    var getModalEndDateEl = document.querySelector("#schedule-end-date");
    var getModalAddBtnEl = document.querySelector(".btn-add-event");
    var getModalUpdateBtnEl = document.querySelector(".btn-update-event");
    var getModalDestroyBtnEl = document.querySelector(".btn-destroy-event");
    var getModalVisibilityEl = document.querySelector("#schedule-visibility");
    var getModalVisibilitySelectionEl = document.getElementById('visibilitySelection');
    var getModalVisibilityInputDivEl = document.getElementById('visibilityInput');
    var getModalVisibilityInputEl = document.getElementById('schedule-visibility-input');
    var getModalForEditableEl = document.querySelector("#foreditable");
    var getModalIsEditableDivEl = document.querySelector(".is_editable");
    var getModalFormEl = document.querySelector("#myForm");

    //   var storeURL = "{{route('schedule.store')}}";

    var calendarsEvents = {
        Danger: "danger",
        Success: "success",
        Primary: "primary",
        Warning: "warning",
    };

    // Calendar Elements and options
    var calendarEl = document.querySelector("#calendar");



    var calendarHeaderToolbar = {
        left: "prev,next",
        center: "title",
        right: "dayGridMonth addEventButton",
    };



    // Calendar Select fn.
    var calendarSelect = function (info) {
        getModalFormEl.setAttribute("action", storeURL);
        var date = new Date(info.startStr);
        var formattedDate = date.toISOString().slice(0, 16);
        getModalStartDateEl.value = formattedDate;
        var date = new Date(info.endStr);
        var formattedDate = date.toISOString().slice(0, 16);
        getModalEndDateEl.value = formattedDate;
        getModalForEditableEl.setAttribute("checked", true);
        getModalAddBtnEl.style.display = "block";
        getModalUpdateBtnEl.style.display = "none";
        getModalDestroyBtnEl.style.display = "none";
        myModal.show();
    };

    // Calendar AddEvent fn.
    var calendarAddEvent = function () {
        getModalFormEl.setAttribute("action", storeURL);
        removeModelData();
        var currentDate = new Date();
        var dd = String(currentDate.getDate()).padStart(2, "0");
        var mm = String(currentDate.getMonth() + 1).padStart(2, "0");
        var yyyy = currentDate.getFullYear();
        var combineDate = `${yyyy}-${mm}-${dd}T00:00:00`;
        var endDate = new Date(currentDate);
        endDate.setDate(currentDate.getDate() + 1);
        var endDd = String(endDate.getDate()).padStart(2, "0");
        var endMm = String(endDate.getMonth() + 1).padStart(2, "0"); // January is 0!
        var endYyyy = endDate.getFullYear();
        var combineEndDate = `${endYyyy}-${endMm}-${endDd}T00:00:00`;
        getModalForEditableEl.setAttribute("checked", true);
        getModalAddBtnEl.style.display = "block";
        getModalUpdateBtnEl.style.display = "none";
        getModalDestroyBtnEl.style.display = "none";
        myModal.show();
        getModalStartDateEl.value = combineDate;
        getModalEndDateEl.value = combineEndDate;
    };

    function setDisable(is_true) {
        if (is_true) {
            getModalFormEl.querySelectorAll('input, textarea').forEach(input => {
                input.setAttribute("disabled", true);
            });
        } else {
            getModalFormEl.querySelectorAll('input, textarea').forEach(input => {
                input.removeAttribute('disabled');
            });
        }
    }

    getModalDestroyBtnEl.addEventListener('click', () => {
        var scheduleID = getModalDestroyBtnEl.getAttribute('data-id');
        var confirmDelete = confirm("Are you sure you want to delete this schedule?");
    
        if (confirmDelete) {
            var form = document.createElement('form');
            form.method = 'POST'; // Use POST method since HTML forms do not support DELETE directly
            form.action = `/schedule/${scheduleID}/destroy`;
    
            // Add a hidden input field to simulate the DELETE method
            var methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
    
            // Add the CSRF token as a hidden input field
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
    
            document.body.appendChild(form);
            form.submit();
        }
    });
    
    
    



    // Calender Event Function
    var calendarEventClick = function (info) {
        removeModelData();

        var eventObj = info.event;
        if (eventObj.url) {
            window.open(eventObj.url);
            info.jsEvent.preventDefault();
        } else {
            var getModalEventId = eventObj._def.publicId;
            var getModalEventLevel = eventObj._def.extendedProps["calendar"];
            var getModalCheckedRadioBtnEl = document.querySelector(
                `input[value="${getModalEventLevel}"]`
            );

            getModalCheckedRadioBtnEl.checked = true;

            getModalTitleEl.value = eventObj.title;
            getModalDescriptionEl.value = eventObj.extendedProps.description;
            getModalUpdateBtnEl.setAttribute(
                "data-fc-event-public-id",
                getModalEventId
            );

            getModalFormEl.setAttribute("action", `/schedule/${getModalEventId}/update`);
            getModalStartDateEl.value = eventObj.extendedProps.start_time;
            getModalEndDateEl.value = eventObj.extendedProps.end_time;
            getModalDestroyBtnEl.setAttribute('data-id',eventObj.id)

            if (eventObj.extendedProps.foreditable) {
                getModalForEditableEl.setAttribute("checked", eventObj.extendedProps.foreditable);
                getModalUpdateBtnEl.style.display = "block";
                setDisable(false);
            } else {
                if (eventObj.extendedProps.is_mine) {
                    getModalUpdateBtnEl.style.display = "block";
                    getModalDestroyBtnEl.style.display = "block";
                    setDisable(false);
                } else {
                    getModalUpdateBtnEl.style.display = "none";
                    getModalDestroyBtnEl.style.display = "none";
                    getModalDestroyBtnEl.setAttribute('data-id','')
                    setDisable(true);
                }
            }

            if (!eventObj.extendedProps.is_mine) {
                if (eventObj.extendedProps.visibility === "private") {
                    getModalVisibilityEl.value = eventObj.extendedProps.visibility;
                    getModalVisibilitySelectionEl.style.display = "block";
                    getModalVisibilityInputDivEl.style.display = "none";
                } else {
                    const visibility = eventObj.extendedProps.visibility.trim().toLowerCase();
                    getModalVisibilityInputEl.value = `${visibility[0].toUpperCase()}${visibility.slice(1)}`;
                    getModalVisibilitySelectionEl.style.display = "none";
                    getModalVisibilityInputDivEl.style.display = "block";
                }
            } else {
                getModalVisibilityEl.value = eventObj.extendedProps.visibility;
                getModalVisibilitySelectionEl.style.display = "block";
                getModalVisibilityInputDivEl.style.display = "none";
            }

            getModalAddBtnEl.style.display = "none";
            myModal.show();
        }
    };

    var checkWidowWidth = () => {
        if (window.innerWidth <= 1199) {
            return true;
        } else {
            return false;
        }
    };

    // Initialize FullCalendar
    var calendar = new FullCalendar.Calendar(calendarEl, {
        selectable: true,
        height: window.innerWidth <= 1199 ? 900 : 1052,
        initialView: window.innerWidth <= 1199 ? "listWeek" : "dayGridMonth",
        initialDate: `${newDate.getFullYear()}-${getDynamicMonth()}-${getDynamicDay()}`,
        headerToolbar: calendarHeaderToolbar,
        events: schedulesData,
        select: calendarSelect,
        unselect: function () {
            console.log("unselected");
        },
        customButtons: {
            addEventButton: {
                text: "Add Schedule",
                click: calendarAddEvent,
            }
        },
        eventClassNames: function ({
            event: calendarEvent
        }) {
            const getColorValue = calendarsEvents[calendarEvent._def.extendedProps.calendar];
            return [
                "event-fc-color fc-bg-" + getColorValue,
            ];
        },
        eventClick: calendarEventClick,
        windowResize: function (arg) {
            if (window.innerWidth <= 1199) {
                calendar.changeView("listWeek");
                calendar.setOption("height", 900);
            } else {
                calendar.changeView("dayGridMonth");
                calendar.setOption("height", 1052);
            }
        },
    });

    calendar.render();

    // Update Calender Event
    getModalUpdateBtnEl.addEventListener("click", function () {
        var getPublicID = this.dataset.fcEventPublicId;
        var getTitleUpdatedValue = getModalTitleEl.value;
        var getEvent = calendar.getEventById(getPublicID);
        var getModalUpdatedCheckedRadioBtnEl = document.querySelector(
            'input[name="schedule_level"]:checked'
        );

        var getModalUpdatedCheckedRadioBtnValue =
            getModalUpdatedCheckedRadioBtnEl !== null ?
            getModalUpdatedCheckedRadioBtnEl.value :
            "";

        getEvent.setProp("title", getTitleUpdatedValue);
        getEvent.setExtendedProp("calendar", getModalUpdatedCheckedRadioBtnValue);
        myModal.hide();
    });


    const removeModelData = () => {
        getModalTitleEl.value = "";
        getModalStartDateEl.value = "";
        getModalEndDateEl.value = "";
        getModalDescriptionEl.value = "";
        getModalForEditableEl.checked = false;
        getModalVisibilitySelectionEl.style.display = "block";
        getModalVisibilityInputDivEl.style.display = "none";
        getModalFormEl.querySelectorAll('input, textarea').forEach(input => {
            input.removeAttribute('disabled');
        });
    };

    // Calendar Init
    calendar.render();
    var myModal = new bootstrap.Modal(document.getElementById("scheduleModal"));
    var modalToggle = document.querySelector(".fc-addEventButton-button ");
    document.getElementById("scheduleModal").addEventListener("hidden.bs.modal", function (event) {
        removeModelData();
    });
});
