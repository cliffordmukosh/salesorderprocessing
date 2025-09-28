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
    <link href="styles.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js" crossorigin="anonymous"></script>

</head>
<body>
<?php include 'header.php'; ?>

<div class="page-container">
    <!-- Main Content -->
    <div class="main-content my-3">
        <!-- Customer Info -->
        <div class="customer-info-row mb-3">
            <div class="customer-card card">
                <div class="card-header">Customer Information</div>
                <div class="card-body" id="customerInfo">
                    <p class="mb-1"><strong>Name:</strong> -</p>
                    <p class="mb-1"><strong>Location:</strong> -</p>
                    <p class="mb-1"><strong>Credit Limit:</strong> 0.00</p>
                    <p class="mb-0"><strong>Account Balance:</strong> 0.00</p>
                </div>
            </div>
            <div class="remarks-container mt-3">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea id="remarks" class="form-control" rows="4"></textarea>
            </div>
        </div>

   <!-- Items Table -->
<div class="card mb-0 flex-grow-1">
    <div class="card-header py-2"><strong>Items</strong></div>
    <div class="table-container">
        <table class="table table-bordered table-sm" id="itemsTableMain">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Original Price</th>
                    <th>Quantity</th>
                    <th>Disc %</th>
                    <th>Price</th>
                    <th>Total Excl</th>
                    <th>Total Incl</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows dynamically inserted here -->
            </tbody>
        </table>
    </div>
</div>


        <!-- Transaction Panel -->
        <div id="transactionPanel" 
             style="position:fixed; bottom:70px; left:20px; width:280px; background:#fff; border:1px solid #ddd; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); padding:14px; z-index:1100; display:none; font-family:Arial, sans-serif;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <h6 style="margin:0; font-size:16px; font-weight:600; color:#333;">Perform a transaction</h6>
                <button type="button" id="closeTransactionPanel" 
                        style="background:none; border:none; font-size:20px; font-weight:bold; color:#666; cursor:pointer; line-height:1;">&times;</button>
            </div>
            <ol style="margin:0; padding-left:20px; font-size:15px; line-height:1.7; color:#444;">
                <li><a href="#" data-transaction="Quote" style="text-decoration:none; color:#0d6efd; font-weight:500;">Quote Transaction</a></li>
                <li><a href="#" data-transaction="Invoice" style="text-decoration:none; color:#0d6efd; font-weight:500;">Invoice Transaction</a></li>
                <li><a href="#" data-transaction="Return" style="text-decoration:none; color:#0d6efd; font-weight:500;">Return Transaction</a></li>
                <li><a href="#" data-transaction="Receive Payment" style="text-decoration:none; color:#0d6efd; font-weight:500;">Receive Payment</a></li>
                <li><a href="#" data-transaction="Recall" style="text-decoration:none; color:#0d6efd; font-weight:500;">Recall</a></li>
                <li><a href="#" data-transaction="Delivery" style="text-decoration:none; color:#0d6efd; font-weight:500;">Delivery</a></li>
            </ol>
        </div>

        <!-- Confirmation box -->
        <div id="transactionConfirmation" 
             style="position:fixed; bottom:140px; left:20px; background:#198754; color:#fff; padding:10px 14px; border-radius:6px; font-size:14px; font-weight:500; display:none; z-index:1200; box-shadow:0 4px 10px rgba(0,0,0,0.2);">
            <span id="confirmationMessage">Transaction started</span>
            <button type="button" id="closeConfirmation" 
                    style="background:none; border:none; color:#fff; font-size:16px; margin-left:10px; cursor:pointer;">&times;</button>
        </div>

        <!-- Top Row: Date + Totals -->
        <div class="totals-row my-3">
            <div class="date-totals-container">
                <div class="date-container">
                    <label class="form-label mb-1">Transaction Date Time</label>
                    <input type="datetime-local" class="form-control form-control-sm" value="<?= date('Y-m-d\TH:i') ?>">
                </div>
                <div class="summary-box text-center">
                    <div class="summary-title">Sub Total</div>
                    <div class="summary-value" id="subTotal">0.00</div>
                </div>
                <div class="summary-box text-center">
                    <div class="summary-title">Tax</div>
                    <div class="summary-value" id="taxTotal">0.00</div>
                </div>
                <div class="summary-box text-center">
                    <div class="summary-title">Total</div>
                    <div class="summary-value" id="grandTotal">0.00</div>
                </div>
            </div>
        </div>
    </div>

<!-- Sidebar -->
<div class="sidebar card p-3" id="sidebar">
  <div class="d-flex flex-column gap-2">
    <button class="btn btn-secondary btn-sm text-start" id="transactionBtn">
      <i class="fas fa-exchange-alt me-2"></i> Transaction
    </button>
    <button class="btn btn-info btn-sm text-start" id="setCustomerBtn" data-bs-toggle="modal" data-bs-target="#customerModal">
      <i class="fas fa-user me-2"></i> Set Customer
    </button>
    <button type="button" class="btn btn-primary btn-sm text-start" id="btnItemLookup" data-bs-toggle="modal" data-bs-target="#itemModal">
      <i class="fas fa-search me-2"></i> Item Lookup
    </button>
    <button class="btn btn-outline-secondary btn-sm text-start" id="saveBtn">
      <i class="fas fa-save me-2"></i> Save
    </button>
    <button class="btn btn-danger btn-sm text-start" id="voidBtn">
      <i class="fas fa-ban me-2"></i> Void
    </button>
    <button class="btn btn-outline-primary btn-sm text-start" id="invoicesBtn">
      <i class="fas fa-file-invoice me-2"></i> Invoices
    </button>
  </div>
</div>


<!-- Sidebar Toggle Button -->
<button class="toggle-btn" id="toggleSidebar">☰ Menu</button>

</div>

<!-- Discount Modal -->
<div class="modal fade" id="discountModal" tabindex="-1" aria-labelledby="discountModalLabel" aria-hidden="true" 
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="discountModalLabel">Set Discount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Discount Type</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="discountType" id="discountPercentage" value="percentage" checked>
                        <label class="form-check-label" for="discountPercentage">Percentage (%)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="discountType" id="discountAmount" value="amount">
                        <label class="form-check-label" for="discountAmount">Amount (KES)</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="discountValue" class="form-label">Discount Value</label>
                    <input type="number" class="form-control" id="discountValue" min="0" step="0.01" placeholder="Enter discount">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="applyDiscountBtn">Apply</button>
            </div>
        </div>
    </div>
</div>

<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" 
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body d-flex">
                <div class="flex-grow-1">
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
                <div class="ms-3 d-flex flex-column align-items-stretch">
                    <button class="btn btn-sm btn-secondary mb-2" id="btnProperties" data-bs-toggle="modal" data-bs-target="#propertiesModal" disabled>Properties</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Properties Modal -->
<div class="modal fade" id="propertiesModal" tabindex="-1" 
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customer Properties</h5>
            </div>
            <div class="modal-body">
                <p><strong>ID:</strong> <span id="propId"></span></p>
                <p><strong>Name:</strong> <span id="propName"></span></p>
                <p><strong>Company:</strong> <span id="propCompany"></span></p>
                <p><strong>Phone:</strong> <span id="propPhone"></span></p>
                <p><strong>Credit Limit:</strong> <span id="propCredit"></span></p>
                <p><strong>Location:</strong> <span id="propLocation"></span></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="closePropertiesBtn">Close</button>
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
                                $taxSql = "SELECT Percentage FROM tax WHERE ID = " . intval($row['TaxID']);
                                $taxResult = $conn->query($taxSql);
                                $taxPercentage = $taxResult && $taxResult->num_rows > 0 ? $taxResult->fetch_assoc()['Percentage'] : 0;
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

<!-- Remove Confirmation Modal -->
<div class="modal fade" id="removeConfirmModal" tabindex="-1" 
     aria-labelledby="removeConfirmModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeConfirmModalLabel">Confirm Removal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="removeConfirmMessage">
                Are you sure you want to remove this item?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmRemoveBtn">Remove</button>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="scripts.js"></script>
</body>
</html>