function input_number(event) {
    if (!(event.charCode >= 48 && event.charCode <= 57)) {
        return event.returnValue = false;
    }
}

function print() {
    var divContents = document.getElementById("print-area").innerHTML;
    // console.log(printArea);
    var a = window.open('', '', 'height=500, width=1000');
    a.document.write('<html>');
    a.document.write('<head>');
    a.document.write(`<style>
        .table-print {
            border-collapse: collapse;
        }
        .table-print tr th {
            background-color: #80f972;
        }
        .table-print tr th, .table-print tr td {
            border: 1px solid black;
            padding: 5px;
        }
    </style>`);
    a.document.write('</head>');
    a.document.write(divContents);
    a.document.write('</body></html>');
    a.document.close();
    a.print();
}