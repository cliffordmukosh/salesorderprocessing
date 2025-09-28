<?php
require __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Return</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 0.9rem; background: #f8f9fa; }
        header, footer { background: #343a40; color: #fff; padding: 8px 15px; }
        footer { font-size: 0.8rem; text-align: center; }
        .customer-card { background: #fff; border: 1px solid #ddd; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .customer-card .card-header { background: #0d6efd; color: #fff; padding: 6px 12px; font-size: 0.9rem; }
        .customer-card .card-body { padding: 12px; font-size: 0.85rem; }
        .summary-box { border: 1px solid #ddd; border-radius: 6px; padding: 12px; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .summary-title { font-size: 0.85rem; font-weight: 600; }
        .summary-value { font-size: 1.1rem; font-weight: bold; }
        .btn-sm { font-size: 0.8rem; min-width: 120px; }
        .table th, .table td { vertical-align: middle; text-align: center; }
        .transaction-panel { display: none; border: 1px solid #ddd; border-radius: 6px; background: #fff; padding: 12px; margin-top: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);}
        .transaction-panel h6 { background: #f8f9fa; padding: 6px; font-size: 0.9rem; margin: -12px -12px 10px -12px; border-bottom: 1px solid #ddd; }
        .transaction-panel a { display: block; padding: 4px 0; color: #0d6efd; text-decoration: none; }
        .transaction-panel a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<header>
    <h6 class="mb-0" id="headerTitle">Sales Order Processing - Sales Return</h6>
</header>

<div class="container my-3">
    <!-- Customer Info -->
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card customer-card">
                <div class="card-header">Customer Information</div>
                <div class="card-body" id="customerInfo">
                    <p class="mb-1"><strong>Name:</strong> -</p>
                    <p class="mb-1"><strong>Location:</strong> -</p>
                    <p class="mb-1"><strong>Credit Limit:</strong> 0.00</p>
                    <p class="mb-0"><strong>Account Balance:</strong> 0.00</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea id="remarks" class="form-control" rows="5"></textarea>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card mb-0">
        <div class="card-header py-2"><strong>Items</strong></div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0" id="itemsTableMain">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Orig. Price (Excl)</th>
                        <th>Disc %</th>
                        <th>Quantity</th>
                        <th>Total Excl</th>
                        <th>Total Incl</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transaction Panel -->
    <div class="transaction-panel" id="transactionPanel">
        <h6>Perform a transaction</h6>
        <ol class="mb-0">
            <li><a href="#" data-transaction="Quote">Quote Transaction</a></li>
            <li><a href="#" data-transaction="Invoice">Invoice Transaction</a></li>
            <li><a href="#" data-transaction="Return">Return Transaction</a></li>
            <li><a href="#" data-transaction="Receive Payment">Receive Payment</a></li>
            <li><a href="#" data-transaction="Recall">Recall</a></li>
        </ol>
    </div>

    <!-- Top Row: Date + Totals -->
    <div class="row g-3 my-3">
        <div class="col-md-3">
            <label class="form-label mb-1">Transaction Date Time</label>
            <input type="datetime-local" class="form-control form-control-sm" value="<?= date('Y-m-d\TH:i') ?>">
        </div>
        <div class="col-md-3">
            <div class="summary-box text-center"><div class="summary-title">Sub Total</div><div class="summary-value" id="subTotal">0.00</div></div>
        </div>
        <div class="col-md-3">
            <div class="summary-box text-center"><div class="summary-title">Tax</div><div class="summary-value" id="taxTotal">0.00</div></div>
        </div>
        <div class="col-md-3">
            <div class="summary-box text-center"><div class="summary-title">Total</div><div class="summary-value" id="grandTotal">0.00</div></div>
        </div>
    </div>

    <!-- Buttons Row -->
    <div class="card p-3 mb-3">
        <div class="d-flex flex-wrap gap-2 justify-content-start">
            <button class="btn btn-secondary btn-sm" id="transactionBtn">Transaction</button>
            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#customerModal">Set Customer</button>
            <button type="button" class="btn btn-primary btn-sm" id="btnItemLookup" data-bs-toggle="modal" data-bs-target="#itemModal">Item Lookup</button>
            <button class="btn btn-outline-secondary btn-sm">Save</button>
            <button class="btn btn-danger btn-sm">Void</button>
            <button class="btn btn-success btn-sm">Delivery</button>
            <button class="btn btn-warning btn-sm">Next Invoice #</button>
            <button class="btn btn-dark btn-sm">Profit</button>
            <button class="btn btn-outline-primary btn-sm">Invoices</button>
        </div>
    </div>
</div>

<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body d-flex">
                <!-- Main content (search + table) -->
                <div class="flex-grow-1">
                    <!-- Lookup/Search -->
                    <div class="mb-2">
                        <input type="text" class="form-control" id="customerSearch" placeholder="Lookup customer...">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm" id="customerTable">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>AccountNumber</th>
                                    <th>FirstName</th>
                                    <th>LastName</th>
                                    <th>Company</th>
                                    <th>PhoneNumber</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $sql = "SELECT ID, AccountNumber, FirstName, LastName, Company, PhoneNumber, City, Country, CreditLimit 
                                    FROM customer LIMIT 50";
                            $result = $conn->query($sql);
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr data-id='{$row['ID']}'
                                              data-name='{$row['FirstName']} {$row['LastName']}'
                                              data-company='{$row['Company']}'
                                              data-phone='{$row['PhoneNumber']}'
                                              data-credit='{$row['CreditLimit']}'
                                              data-location='{$row['City']}, {$row['Country']}'>
                                              <td>{$row['ID']}</td>
                                              <td>{$row['AccountNumber']}</td>
                                              <td>{$row['FirstName']}</td>
                                              <td>{$row['LastName']}</td>
                                              <td>{$row['Company']}</td>
                                              <td>{$row['PhoneNumber']}</td>
                                          </tr>";
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Vertical button group -->
                <div class="ms-3 d-flex flex-column align-items-stretch">
                    <button class="btn btn-sm btn-primary mb-2" id="btnNew" data-bs-toggle="modal" data-bs-target="#newCustomerModal">New</button>
                    <button class="btn btn-sm btn-secondary mb-2" id="btnProperties" data-bs-toggle="modal" data-bs-target="#propertiesModal" disabled>Properties</button>
                    <button class="btn btn-sm btn-success mb-2" id="btnApprove" data-bs-toggle="modal" data-bs-target="#approveModal" disabled>Approve</button>
                    <button class="btn btn-sm btn-danger mb-2" id="btnDelete" data-bs-toggle="modal" data-bs-target="#deleteModal" disabled>Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Child Modals -->
<div class="modal fade" id="newCustomerModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">New Customer</h5></div>
            <div class="modal-body">Form to add a new customer goes here...</div>
            <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="propertiesModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Customer Properties</h5></div>
            <div class="modal-body">
                <p><strong>ID:</strong> <span id="propId"></span></p>
                <p><strong>Name:</strong> <span id="propName"></span></p>
                <p><strong>Company:</strong> <span id="propCompany"></span></p>
                <p><strong>Phone:</strong> <span id="propPhone"></span></p>
                <p><strong>Credit Limit:</strong> <span id="propCredit"></span></p>
                <p><strong>Location:</strong> <span id="propLocation"></span></p>
            </div>
            <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="approveModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Approve Customer</h5></div>
            <div class="modal-body">Customer <span id="approveName"></span> has been approved ✅</div>
            <div class="modal-footer"><button class="btn btn-success" data-bs-dismiss="modal">OK</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Delete Customer</h5></div>
            <div class="modal-body">Are you sure you want to delete <span id="deleteName"></span>?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Item Modal -->
<div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemModalLabel">Select Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchItem" class="form-control mb-2" placeholder="Search items...">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Description</th>
                                <th>Available Qty</th>
                                <th>Price (Excl)</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql = "SELECT ItemLookupCode, Description, quantity, Price, TaxID FROM item WHERE Inactive = 0";
                        $result = $conn->query($sql);
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Fetch tax percentage for each item
                                $taxSql = "SELECT Percentage FROM tax WHERE ID = " . intval($row['TaxID']);
                                $taxResult = $conn->query($taxSql);
                                $taxPercentage = $taxResult && $taxResult->num_rows > 0 ? $taxResult->fetch_assoc()['Percentage'] : 0;

                                // Calculate VAT-exclusive price
                                $priceIncl = floatval($row['Price']);
                                $taxRate = $taxPercentage / 100;
                                $priceExcl = $priceIncl / (1 + $taxRate);
                                $taxAmount = $priceIncl - $priceExcl;

                                echo "<tr data-code='{$row['ItemLookupCode']}' 
                                        data-description='{$row['Description']}' 
                                        data-quantity='{$row['quantity']}' 
                                        data-price-incl='{$priceIncl}' 
                                        data-price-excl='" . number_format($priceExcl, 2, '.', '') . "' 
                                        data-taxamount='" . number_format($taxAmount, 2, '.', '') . "' 
                                        data-taxpercentage='{$taxPercentage}'>
                                        <td>{$row['ItemLookupCode']}</td>
                                        <td>{$row['Description']}</td>
                                        <td>{$row['quantity']}</td>
                                        <td>" . number_format($priceExcl, 2) . "</td>
                                      </tr>";
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<footer>
    <small>&copy; <?= date("Y"); ?> Sales System. All Rights Reserved.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Customer modal row selection
    document.addEventListener("DOMContentLoaded", function() {
        const table = document.getElementById("customerTable");
        const buttons = ["btnProperties", "btnApprove", "btnDelete"].map(id => document.getElementById(id));
        let selectedRow = null;

        table.addEventListener("click", function(e) {
            const row = e.target.closest("tr");
            if (!row) return;
            table.querySelectorAll("tr").forEach(r => r.classList.remove("table-active"));
            row.classList.add("table-active");
            selectedRow = row;
            buttons.forEach(btn => btn.removeAttribute("disabled"));
        });

        // Pass data to child modals
        document.getElementById("btnProperties").addEventListener("click", function() {
            if (selectedRow) {
                document.getElementById("propId").innerText = selectedRow.dataset.id;
                document.getElementById("propName").innerText = selectedRow.dataset.name;
                document.getElementById("propCompany").innerText = selectedRow.dataset.company;
                document.getElementById("propPhone").innerText = selectedRow.dataset.phone;
                document.getElementById("propCredit").innerText = selectedRow.dataset.credit;
                document.getElementById("propLocation").innerText = selectedRow.dataset.location;
            }
        });

        document.getElementById("btnApprove").addEventListener("click", function() {
            if (selectedRow) {
                document.getElementById("approveName").innerText = selectedRow.dataset.name;
            }
        });

        document.getElementById("btnDelete").addEventListener("click", function() {
            if (selectedRow) {
                document.getElementById("deleteName").innerText = selectedRow.dataset.name;
            }
        });

        // Reopen parent modal when a child closes
        ['newCustomerModal', 'propertiesModal', 'approveModal', 'deleteModal'].forEach(id => {
            const modalEl = document.getElementById(id);
            modalEl.addEventListener('hidden.bs.modal', function () {
                const parentModal = new bootstrap.Modal(document.getElementById('customerModal'));
                parentModal.show();
            });
        });

        // Customer search filter
        document.getElementById("customerSearch").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            table.querySelectorAll("tbody tr").forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
            });
        });

        // Double click customer row -> set info
        document.querySelectorAll("#customerTable tbody tr").forEach(row => {
            row.addEventListener("dblclick", function() {
                let name = this.dataset.name;
                let company = this.dataset.company;
                let phone = this.dataset.phone;
                let credit = this.dataset.credit || "0.00";
                let location = this.dataset.location;

                let info = `
                    <p class="mb-1"><strong>Name:</strong> ${name}</p>
                    <p class="mb-1"><strong>Location:</strong> ${location}</p>
                    <p class="mb-1"><strong>Credit Limit:</strong> ${credit}</p>
                    <p class="mb-0"><strong>Account Balance:</strong> 0.00</p>
                `;
                document.getElementById("customerInfo").innerHTML = info;
                let modal = bootstrap.Modal.getInstance(document.getElementById("customerModal"));
                modal.hide();
            });
        });
    });

    // Toggle transaction panel
    document.getElementById("transactionBtn").addEventListener("click", function () {
        let panel = document.getElementById("transactionPanel");
        panel.style.display = panel.style.display === "none" || panel.style.display === "" ? "block" : "none";
        if (panel.style.display === "block") {
            panel.scrollIntoView({ behavior: "smooth", block: "start" });
        }
    });

    // Update header title on transaction link click
    document.querySelectorAll("#transactionPanel a").forEach(link => {
        link.addEventListener("click", function(e) {
            e.preventDefault();
            let transactionType = this.dataset.transaction;
            document.getElementById("headerTitle").textContent = `Sales Order Processing - ${transactionType}`;
        });
    });

    // Item search functionality
    document.getElementById("searchItem").addEventListener("input", function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#itemsTable tbody tr");
        rows.forEach(row => {
            let code = row.dataset.code.toLowerCase();
            let description = row.dataset.description.toLowerCase();
            row.style.display = (code.includes(filter) || description.includes(filter)) ? "" : "none";
        });
    });

    // Double click item row -> add to main table
    let itemCounter = 0;
    document.querySelectorAll("#itemsTable tbody tr").forEach(row => {
        row.addEventListener("dblclick", function() {
            let code = this.dataset.code;
            let description = this.dataset.description;
            let priceExcl = parseFloat(this.dataset.priceExcl);
            let taxPercentage = parseFloat(this.dataset.taxpercentage) || 0;
            let quantity = 1; // Default quantity
            let discount = 0; // Default discount percentage

            // Calculate values
            let discPriceExcl = priceExcl * (1 - discount / 100);
            let totalExcl = discPriceExcl * quantity;
            let totalIncl = totalExcl * (1 + taxPercentage / 100);

            itemCounter++;
            let newRow = `
                <tr data-row-id="${itemCounter}" data-taxpercentage="${taxPercentage}">
                    <td>${itemCounter}</td>
                    <td>${code}</td>
                    <td>${description}</td>
                    <td>${priceExcl.toFixed(2)}</td>
                    <td><input type="number" class="form-control form-control-sm discount-input" value="${discount}" min="0" max="100"></td>
                    <td><input type="number" class="form-control form-control-sm quantity-input" value="${quantity}" min="1"></td>
                    <td class="total-excl">${totalExcl.toFixed(2)}</td>
                    <td class="total-incl">${totalIncl.toFixed(2)}</td>
                    <td><button class="btn btn-danger btn-sm remove-item">Remove</button></td>
                </tr>
            `;
            document.querySelector("#itemsTableMain tbody").insertAdjacentHTML("beforeend", newRow);
            updateTotals();

            // Close modal
            let modal = bootstrap.Modal.getInstance(document.getElementById("itemModal"));
            modal.hide();
        });
    });

    // Update totals when quantity or discount changes
    document.addEventListener("input", function(e) {
        if (e.target.classList.contains("quantity-input") || e.target.classList.contains("discount-input")) {
            let row = e.target.closest("tr");
            let priceExcl = parseFloat(row.cells[3].textContent);
            let quantity = parseFloat(row.querySelector(".quantity-input").value) || 1;
            let discount = parseFloat(row.querySelector(".discount-input").value) || 0;
            let taxPercentage = parseFloat(row.dataset.taxpercentage) || 0;

            // Calculate values
            let discPriceExcl = priceExcl * (1 - discount / 100);
            let totalExcl = discPriceExcl * quantity;
            let totalIncl = totalExcl * (1 + taxPercentage / 100);

            // Update row
            row.querySelector(".total-excl").textContent = totalExcl.toFixed(2);
            row.querySelector(".total-incl").textContent = totalIncl.toFixed(2);
            updateTotals();
        }
    });

    // Remove item
    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("remove-item")) {
            e.target.closest("tr").remove();
            // Re-number rows
            document.querySelectorAll("#itemsTableMain tbody tr").forEach((row, index) => {
                row.cells[0].textContent = index + 1;
                row.dataset.rowId = index + 1;
            });
            itemCounter = document.querySelectorAll("#itemsTableMain tbody tr").length;
            updateTotals();
        }
    });

    // Update totals
    function updateTotals() {
        let subTotal = 0;
        let taxTotal = 0;
        let grandTotal = 0;

        document.querySelectorAll("#itemsTableMain tbody tr").forEach(row => {
            let totalExcl = parseFloat(row.querySelector(".total-excl").textContent) || 0;
            let totalIncl = parseFloat(row.querySelector(".total-incl").textContent) || 0;
            let tax = totalIncl - totalExcl;
            subTotal += totalExcl;
            taxTotal += tax;
            grandTotal += totalIncl;
        });

        document.getElementById("subTotal").textContent = subTotal.toFixed(2);
        document.getElementById("taxTotal").textContent = taxTotal.toFixed(2);
        document.getElementById("grandTotal").textContent = grandTotal.toFixed(2);
    }
</script>
</body>
</html>