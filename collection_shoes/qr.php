<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Form</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f9;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .form-container {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 300px;
    }

    .form-container h2 {
        margin-bottom: 20px;
        text-align: center;
    }

    .form-container input {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .form-container button {
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .form-container button:hover {
        background-color: #0056b3;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .modal-content img {
        width: 200px;
        height: 200px;
    }

    .modal-content button {
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .modal-content button:hover {
        background-color: #0056b3;
    }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Payment Form</h2>
        <form id="paymentForm">
            <input type="text" id="price" placeholder="Enter Price" required>
            <input type="text" id="description" placeholder="Enter Description" required>
            <button type="button" onclick="showModal()">Make Payment</button>
        </form>
    </div>

    <div class="modal" id="qrModal">
        <div class="modal-content">
            <h3>Payment QR Code</h3>
            <img id="qrCode" src="" alt="QR Code">
            <p id="modalPrice"></p>
            <p>Scan this QR code to complete your payment.</p>
            <button onclick="closeModal()">Close</button>
        </div>
    </div>

    <script>
    function showModal() {
        const price = document.getElementById('price').value.trim();
        const description = document.getElementById('description').value.trim();

        if (!price || !description) {
            alert('Please fill in all fields!');
            return;
        }

        // Append currency symbol to the price
        const formattedPrice = `${price} $`;
        const paymentData = `Price: ${formattedPrice}, Description: ${description}`;

        // Generate QR code URL using a free API
        const qrCodeUrl =
            `https://api.qrserver.com/v1/create-qr-code/?data=${encodeURIComponent(paymentData)}&size=200x200`;

        // Set the QR code image source
        const qrCodeImg = document.getElementById('qrCode');
        qrCodeImg.src = qrCodeUrl;
        qrCodeImg.alt = `QR Code for Price: ${formattedPrice}, Description: ${description}`;

        // Display the price in the modal
        const modalPrice = document.getElementById('modalPrice');
        modalPrice.textContent = `Price: ${formattedPrice}`;

        // Display the modal
        document.getElementById('qrModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('qrModal').style.display = 'none';
    }
    </script>
</body>

</html>