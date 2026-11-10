
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments Management</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            background: #f4f6f9;
            color: #333;
        }

        header {
            background: #263b59;
            color: white;
            padding: 22px 35px;
        }

        header h2 {
            margin: 0;
        }

        .container {
            padding: 30px;
            max-width: 1100px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 22px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px #00000010;
        }

        input, select {
            padding: 11px;
            width: 100%;
            margin: 7px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px 16px;
            border: none;
            border-radius: 5px;
            background: #263b59;
            color: white;
            cursor: pointer;
        }

        button:hover {
            opacity: .85;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 13px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f4f6f9;
        }

        .delete {
            background: #dc3545;
        }

        .edit {
            background: #e0a800;
            color: #222;
        }

        #message {
            margin-bottom: 15px;
        }

        .table-wrap {
            overflow-x: auto;
        }
    </style>
</head>

<body>

<header>
    <h2>Payments Management System</h2>
    <p>Manage and monitor customer payments</p>
</header>

<div class="container">

    <div class="card">
        <h3>Add Payment</h3>

        <form id="paymentForm">
            <input type="hidden" id="paymentId">

            <label>Order Reference</label>
            <input type="text" id="order_reference" required>

            <label>Amount</label>
            <input type="number" id="amount" min="0.01" step="0.01" required>

            <label>Payment Method</label>
            <select id="payment_method" required>
                <option value="">Select payment method</option>
                <option value="Cash">Cash</option>
                <option value="GCash">GCash</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>

            <label>Status</label>
            <select id="status">
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="failed">Failed</option>
            </select>

            <label>Reference Number</label>
            <input type="text" id="reference_number">

            <button type="submit" id="saveButton">Save Payment</button>
            <button type="button" onclick="resetForm()">Clear</button>
        </form>
    </div>

    <div class="card">
        <h3>Payment Records</h3>
        <p id="message"></p>

        <button onclick="loadPayments()">Refresh Records</button>

        <br><br>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Order Reference</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Reference No.</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody id="paymentTable">
                    <tr>
                        <td colspan="7">Loading payments...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
const API_URL = '/api/payments';

const form = document.getElementById('paymentForm');
const table = document.getElementById('paymentTable');
const message = document.getElementById('message');

async function loadPayments() {
    try {
        const response = await fetch(API_URL);
        const data = await response.json();

        if (!response.ok) {
            throw new Error('Unable to load payments.');
        }

        table.innerHTML = '';

        const payments = data.payments || [];

        if (payments.length === 0) {
            table.innerHTML =
                '<tr><td colspan="7">No payment records found.</td></tr>';
            return;
        }

        payments.forEach(payment => {
            const row = document.createElement('tr');

            const values = [
                payment.id,
                payment.order_reference,
                '₱' + Number(payment.amount).toFixed(2),
                payment.payment_method,
                payment.status,
                payment.reference_number || '-'
            ];

            values.forEach(value => {
                const cell = document.createElement('td');
                cell.textContent = value;
                row.appendChild(cell);
            });

            const actions = document.createElement('td');

            const editButton = document.createElement('button');
            editButton.textContent = 'Edit';
            editButton.className = 'edit';
            editButton.onclick = () => editPayment(payment);

            const deleteButton = document.createElement('button');
            deleteButton.textContent = 'Delete';
            deleteButton.className = 'delete';
            deleteButton.onclick = () => deletePayment(payment.id);

            actions.append(editButton, document.createTextNode(' '), deleteButton);
            row.appendChild(actions);

            table.appendChild(row);
        });

    } catch (error) {
        message.textContent = error.message;
    }
}

form.addEventListener('submit', async function(event) {
    event.preventDefault();

    const id = document.getElementById('paymentId').value;

    const payment = {
        order_reference: document.getElementById('order_reference').value,
        amount: document.getElementById('amount').value,
        payment_method: document.getElementById('payment_method').value,
        status: document.getElementById('status').value,
        reference_number: document.getElementById('reference_number').value || null
    };

    try {
        const response = await fetch(id ? `${API_URL}/${id}` : API_URL, {
            method: id ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payment)
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Unable to save payment.');
        }

        message.textContent = data.message || 'Payment saved successfully.';
        resetForm();
        loadPayments();

    } catch (error) {
        message.textContent = error.message;
    }
});

function editPayment(payment) {
    document.getElementById('paymentId').value = payment.id;
    document.getElementById('order_reference').value = payment.order_reference;
    document.getElementById('amount').value = payment.amount;
    document.getElementById('payment_method').value = payment.payment_method;
    document.getElementById('status').value = payment.status;
    document.getElementById('reference_number').value = payment.reference_number || '';

    document.getElementById('saveButton').textContent = 'Update Payment';
    window.scrollTo(0, 0);
}

async function deletePayment(id) {
    if (!confirm('Are you sure you want to delete this payment?')) {
        return;
    }

    try {
        const response = await fetch(`${API_URL}/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Unable to delete payment.');
        }

        message.textContent = data.message;
        loadPayments();

    } catch (error) {
        message.textContent = error.message;
    }
}

function resetForm() {
    form.reset();
    document.getElementById('paymentId').value = '';
    document.getElementById('saveButton').textContent = 'Save Payment';
}

loadPayments();
</script>

</body>
</html>
