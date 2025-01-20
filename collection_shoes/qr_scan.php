<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Form</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg, #74ebd5, #9face6);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .form-container {
        background: #ffffff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        width: 360px;
        text-align: center;
    }

    .form-container h2 {
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
    }

    .form-container input {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
    }

    .form-container button {
        width: 100%;
        padding: 12px;
        background: #4caf50;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .form-container button:hover {
        background: #45a049;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: #ffffff;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        width: 340px;
    }

    .modal-content canvas {
        margin-bottom: 15px;
    }

    .modal-content p {
        font-size: 18px;
        margin: 10px 0;
        color: #555;
    }

    .modal-content button {
        padding: 10px 20px;
        background: #f44336;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .modal-content button:hover {
        background: #d32f2f;
    }

    @media (max-width: 400px) {

        .form-container,
        .modal-content {
            width: 90%;
        }
    }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    <script src="https://github.com/davidhuotkeo/bakong-khqr/releases/download/bakong-khqr-1.0.6/khqr-1.0.6.min.js">
    </script>
</head>

<body>
    <div class="form-container">
        <h2>Payment Form</h2>
        <form id="paymentForm">
            <input type="number" id="amount" placeholder="Enter Amount" required>
            <input type="text" id="description" placeholder="Enter Description" required>
            <button type="button" id="generateQR">Generate QR Code</button>
        </form>
    </div>

    <div class="modal" id="qrModal">
        <div class="modal-content">
            <h3>Payment QR Code</h3>
            <canvas id="qrCode"></canvas>
            <p id="getamount"></p>
            <button onclick="closeModal()">Close</button>
        </div>
    </div>

    <script>
    document.getElementById('generateQR').addEventListener('click', function() {
        const amount = document.getElementById('amount').value.trim();
        const description = document.getElementById('description').value.trim();

        if (amount === '' || description === '') {
            alert('Please fill in all fields!');
            return;
        }

        // --------- Generate KHQR ---------
        const KHQR = BakongKHQR;
        const data = KHQR.khqrData;
        const Info = KHQR.IndividualInfo;

        const optionalData = {
            currency: data.currency.khr,
            amount: amount,
            mobileNumber: "0716993037",
            storeLabel: "AAO",
            terminalLabel: "Acc1",
            languagePreference: "km",
        };

        const individualInfo = new Info(
            "sokha_long2@cpbl",
            "SOKHA LONG",
            "PHNOM PENH",
            optionalData
        );

        const khqrInstance = new KHQR.BakongKHQR();
        const individual = khqrInstance.generateIndividual(individualInfo);

        console.log(individual);
        // --------- End ---------

        document.getElementById('qrModal').style.display = 'flex';
        QRCode.toCanvas(document.getElementById('qrCode'), individual.data.qr);
        document.getElementById('getamount').textContent =
            `Amount: ${amount} Riel\nDescription: ${description}`;
    });

    function closeModal() {
        document.getElementById('qrModal').style.display = 'none';
    }
    // let md5value = individual ? individual.data.md5 : null;
    // let checkTransactionInterval;

    // function startQRCodeScanner() {
    //     if (md5value) {
    //         checkTransactionInterval = setInterval(() => {
    //             fetchTransactionStatus(md5value);
    //         }, 3000);
    //     } else {
    //         console.error("MD5 value is not available.")
    //     }
    // }
    // const check_transaction = setInterval(function() {
    //     $.ajax({
    //         url: "https://api-bakong.nbc.gov.kh/v1/check_transaction_by_md5",
    //         type: "POST",
    //         data: {
    //             "md5": individua.data.md5,
    //         },
    //         headers: {
    //             cotentType: "application/json",
    //             Authorization: "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7ImlkIjoiMjIyNDlmYTlhMzQ2NDYzNSJ9LCJpYXQiOjE3MzYxNjI5OTgsImV4cCI6MTc0MzkzODk5OH0.fRpexP-sRly7fIE4ASpEKoQXvYnhosyDwYU3cAZAA5w",

    //         },
    //         success: function(response) {
    //             console.log(response);
    //             if (response.responseMessage == "Success") {
    //                 window.location.href = '/';
    //                 clearInterval(check_transaction);
    //                 $('qrModel').model('hide');

    //             }
    //         },
    //         error: function(response) {
    //             console.log(response);
    //         }
    //     })
    // }, 1000);
    const check_transaction = setInterval(function() {
        $.ajax({
            url: "https://api-bakong.nbc.gov.kh/v1/check_transaction_by_md5",
            type: "POST",
            data: JSON.stringify({
                "md5": individual.data.md5,
            }),
            headers: {
                contentType: "application/json",
                Authorization: "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7ImlkIjoiMjIyNDlmYTlhMzQ2NDYzNSJ9LCJpYXQiOjE3MzYxNjI5OTgsImV4cCI6MTc0MzkzODk5OH0.fRpexP-sRly7fIE4ASpEKoQXvYnhosyDwYU3cAZAA5w",
            },
            success: function(response) {
                console.log(response);
                if (response.responseMessage == "Success") {
                    clearInterval(check_transaction); // Stop checking
                    document.getElementById('qrModal').style.display = 'none'; // Hide QR modal
                    window.location.href = '/'; // Redirect to homepage
                }
            },
            error: function(response) {
                console.log(response);
            }

        });
    }, 1000);
    </script>
</body>

</html>