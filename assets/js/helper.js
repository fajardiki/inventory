function input_number(event) {
    if (!(event.charCode >= 48 && event.charCode <= 57)) {
        return event.returnValue = false;
    }
}