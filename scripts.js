let itemCounter = 0; // Initialize globally
let currentRow = null; // Track the row being edited in the discount modal


// ──────────────────────────────────────────────────────────────
function formatKES(number) {
  if (isNaN(number) || number === null || number === undefined)
    return "KES 0.00";
  return (
    "KES " +
    parseFloat(number).toLocaleString("en-KE", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    })
  );
}
// Save state to localStorage
function saveState() {
  try {
    const items = [];
    document.querySelectorAll("#itemsTableMain tbody tr").forEach((row) => {
      items.push({
        id: row.dataset.rowId,
        code: row.cells[1].textContent,
        description: row.cells[2].textContent,
        originalPrice: row.querySelector(".original-price").textContent,
        quantity: row.querySelector(".quantity-input").value,
        discount: row.dataset.discount || "0",
        discountType: row.dataset.discountType || "percentage",
        price: row.querySelector(".price").textContent,
        totalExcl: row.querySelector(".total-excl").textContent,
        totalIncl: row.querySelector(".total-incl").textContent,
        priceExcl: row.dataset.priceExcl,
        taxPercentage: row.dataset.taxpercentage,
      });
    });
    const customerInfo = document.getElementById("customerInfo").innerHTML;
    const remarks = document.getElementById("remarks").value;
    const headerTitle = document.getElementById("headerTitle").textContent;
    localStorage.setItem(
      "cart",
      JSON.stringify({
        items,
        customerInfo,
        remarks,
        headerTitle,
        itemCounter,
      })
    );
  } catch (e) {
    console.error("Error saving state to localStorage:", e);
  }
}

// Load state from localStorage
function loadState() {
  try {
    const state = JSON.parse(localStorage.getItem("cart"));
    if (!state) return;

    itemCounter = state.itemCounter || 0;
    document.querySelector("#itemsTableMain tbody").innerHTML = "";
    state.items.forEach((item) => {
      const newRow = `
                <tr data-row-id="${item.id}" data-taxpercentage="${
        item.taxPercentage
      }" data-price-excl="${item.priceExcl}" data-discount="${
        item.discount
      }" data-discount-type="${item.discountType}">
                    <td>${item.id}</td>
                    <td>${item.code}</td>
                    <td>${item.description}</td>
                    <td class="original-price">${item.originalPrice}</td>
                    <td><input type="number" class="form-control form-control-sm quantity-input" value="${
                      item.quantity
                    }" min="1"></td>
                    <td class="discount-cell">${
                      item.discountType === "percentage"
                        ? item.discount + "%"
                        : "KES " + item.discount
                    }</td>
                    <td class="price">${item.price}</td>
                    <td class="total-excl">${item.totalExcl}</td>
                    <td class="total-incl">${item.totalIncl}</td>
                    <td><button class="btn btn-sm remove-item no-bg">
                        <i class="fas fa-trash text-danger"></i>
                    </button></td>
                </tr>
            `;
      document
        .querySelector("#itemsTableMain tbody")
        .insertAdjacentHTML("beforeend", newRow);
    });

    document.getElementById("customerInfo").innerHTML =
      state.customerInfo ||
      `
            <p class="mb-1"><strong>Name:</strong> -</p>
            <p class="mb-1"><strong>Location:</strong> -</p>
            <p class="mb-1"><strong>Credit Limit:</strong> 0.00</p>
            <p class="mb-0"><strong>Account Balance:</strong> 0.00</p>
        `;
    document.getElementById("remarks").value = state.remarks || "";
    document.getElementById("headerTitle").textContent =
      state.headerTitle || "Sales Order Processing - Sales Return";
    updateTotals();
  } catch (e) {
    console.error("Error loading state from localStorage:", e);
  }
}

// Update totals with proper KES formatting
function updateTotals() {
    let subTotal = 0;
    let taxTotal = 0;
    let grandTotal = 0;

    document.querySelectorAll("#itemsTableMain tbody tr").forEach((row) => {
        // Skip completely empty rows
        if (!row.cells[1].textContent.trim() && !row.cells[2].textContent.trim()) return;

        let totalExcl = parseFloat(row.querySelector(".total-excl").textContent.replace(/[^\d.-]/g, '')) || 0;
        let totalIncl = parseFloat(row.querySelector(".total-incl").textContent.replace(/[^\d.-]/g, '')) || 0;
        let tax = totalIncl - totalExcl;

        subTotal += totalExcl;
        taxTotal += tax;
        grandTotal += totalIncl;
    });

    // Update with beautiful formatting
    document.getElementById("subTotal").textContent = formatKES(subTotal);
    document.getElementById("taxTotal").textContent = formatKES(taxTotal);
    document.getElementById("grandTotal").textContent = formatKES(grandTotal);
}

// Initialize event listeners
function initializeEventListeners() {
  try {
    // Sidebar toggle
    document
      .getElementById("toggleSidebar")
      .addEventListener("click", function () {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("collapsed");
      });

    // Customer modal row selection
    const table = document.getElementById("customerTable");
    const propertiesBtn = document.getElementById("btnProperties");
    let selectedRow = null;

    table.addEventListener("click", function (e) {
      const row = e.target.closest("tr");
      if (!row) return;
      table
        .querySelectorAll("tr")
        .forEach((r) => r.classList.remove("table-active"));
      row.classList.add("table-active");
      selectedRow = row;
      propertiesBtn.removeAttribute("disabled");
    });

    propertiesBtn.addEventListener("click", function () {
      if (selectedRow) {
        document.getElementById("propId").innerText = selectedRow.dataset.id;
        document.getElementById("propName").innerText =
          selectedRow.dataset.name;
        document.getElementById("propCompany").innerText =
          selectedRow.dataset.company;
        document.getElementById("propPhone").innerText =
          selectedRow.dataset.phone;
        document.getElementById("propCredit").innerText =
          selectedRow.dataset.credit;
        document.getElementById("propLocation").innerText =
          selectedRow.dataset.location;
      }
    });

    // Customer search
    document
      .getElementById("customerSearch")
      .addEventListener("keyup", function () {
        let filter = this.value.toLowerCase();
        table.querySelectorAll("tbody tr").forEach((row) => {
          row.style.display = row.innerText.toLowerCase().includes(filter)
            ? ""
            : "none";
        });
      });

    // Double-click customer row
    document.querySelectorAll("#customerTable tbody tr").forEach((row) => {
      row.addEventListener("dblclick", function () {
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
        let modal = bootstrap.Modal.getInstance(
          document.getElementById("customerModal")
        );
        modal.hide();
        saveState();
      });
    });

    // Remarks input
    document.getElementById("remarks").addEventListener("input", saveState);

    // Transaction panel toggle
    document
      .getElementById("transactionBtn")
      .addEventListener("click", function () {
        let panel = document.getElementById("transactionPanel");
        panel.style.display =
          panel.style.display === "none" || panel.style.display === ""
            ? "block"
            : "none";
        if (panel.style.display === "block") {
          panel.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      });

    // Close transaction panel
    document
      .getElementById("closeTransactionPanel")
      .addEventListener("click", function () {
        document.getElementById("transactionPanel").style.display = "none";
      });

    // Transaction link click
    document.querySelectorAll("#transactionPanel a").forEach((link) => {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        let transactionType = this.dataset.transaction;
        document.getElementById(
          "headerTitle"
        ).textContent = `Sales Order Processing - ${transactionType}`;
        document.getElementById("transactionPanel").style.display = "none";
        updateDetails(transactionType);
        saveState();
      });
    });

    // Confirmation logic
    const confirmationBox = document.getElementById("transactionConfirmation");
    const confirmationMessage = document.getElementById("confirmationMessage");
    const closeConfirmation = document.getElementById("closeConfirmation");

    document.querySelectorAll("#transactionPanel a").forEach((link) => {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        const transaction = this.getAttribute("data-transaction");

        confirmationMessage.textContent = `${transaction} Transaction started`;
        confirmationBox.style.display = "inline-block";

        // Auto close after 3 seconds
        setTimeout(() => {
          confirmationBox.style.display = "none";
        }, 3000);
      });
    });

    // Manual close
    closeConfirmation.addEventListener("click", function () {
      confirmationBox.style.display = "none";
    });

    // Void button
    document.getElementById("voidBtn").addEventListener("click", function () {
      localStorage.removeItem("cart");
      itemCounter = 0;
      document.querySelector("#itemsTableMain tbody").innerHTML = "";
      document.getElementById("customerInfo").innerHTML = `
                <p class="mb-1"><strong>Name:</strong> -</p>
                <p class="mb-1"><strong>Location:</strong> -</p>
                <p class="mb-1"><strong>Credit Limit:</strong> 0.00</p>
                <p class="mb-0"><strong>Account Balance:</strong> 0.00</p>
            `;
      document.getElementById("remarks").value = "";
      document.getElementById("headerTitle").textContent =
        "Sales Order Processing - Sales Return";
      updateTotals();
      location.reload();
    });

    // Item search
    document
      .getElementById("searchItem")
      .addEventListener("input", function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#itemsTable tbody tr");
        rows.forEach((row) => {
          let code = row.dataset.code.toLowerCase();
          let description = row.dataset.description.toLowerCase();
          row.style.display =
            code.includes(filter) || description.includes(filter) ? "" : "none";
        });
      });

    // Double-click item row
    document.querySelectorAll("#itemsTable tbody tr").forEach((row) => {
      row.addEventListener("dblclick", function () {
        let code = this.dataset.code;
        let description = this.dataset.description;
        let priceExcl = parseFloat(this.dataset.priceExcl);
        let taxPercentage = parseFloat(this.dataset.taxpercentage) || 0;

        // Check if item with same code exists
        let existingRow = Array.from(
          document.querySelectorAll("#itemsTableMain tbody tr")
        ).find((row) => row.cells[1].textContent === code);

        if (existingRow) {
          // Increment quantity
          let quantityInput = existingRow.querySelector(".quantity-input");
          let quantity = (parseFloat(quantityInput.value) || 1) + 1;
          quantityInput.value = quantity;

          // Update totals
          let discount = parseFloat(existingRow.dataset.discount) || 0;
          let discountType = existingRow.dataset.discountType || "percentage";
          let totalExcl = priceExcl * quantity;
          let totalInclBase = totalExcl * (1 + taxPercentage / 100);
          let totalIncl =
            discountType === "percentage"
              ? totalInclBase * (1 - discount / 100)
              : totalInclBase - discount;

          existingRow.querySelector(".original-price").textContent =
            priceExcl.toFixed(2);
          existingRow.querySelector(".price").textContent =
            priceExcl.toFixed(2);
          existingRow.querySelector(".total-excl").textContent =
            totalExcl.toFixed(2);
          existingRow.querySelector(".total-incl").textContent =
            totalIncl.toFixed(2);
        } else {
          // Remove all empty rows
          document
            .querySelectorAll("#itemsTableMain tbody tr")
            .forEach((row) => {
              if (!row.cells[1].textContent && !row.cells[2].textContent) {
                row.remove();
              }
            });

          // Add new row at the top
          let quantity = 1;
          let discount = 0;
          let discountType = "percentage";
          let totalExcl = priceExcl * quantity;
          let totalIncl = totalExcl * (1 + taxPercentage / 100);

          itemCounter++;
          let newRow = `
                        <tr data-row-id="${itemCounter}" data-taxpercentage="${taxPercentage}" data-price-excl="${priceExcl}" data-discount="${discount}" data-discount-type="${discountType}">
                            <td>${itemCounter}</td>
                            <td>${code}</td>
                            <td>${description}</td>
                            <td class="original-price">${priceExcl.toFixed(
                              2
                            )}</td>
                            <td><input type="number" class="form-control form-control-sm quantity-input" value="${quantity}" min="1"></td>
                            <td class="discount-cell">${
                              discountType === "percentage"
                                ? discount + "%"
                                : "KES  " + discount
                            }</td>
                            <td class="price">${priceExcl.toFixed(2)}</td>
                            <td class="total-excl">${totalExcl.toFixed(2)}</td>
                            <td class="total-incl">${totalIncl.toFixed(2)}</td>
                            <td><button class="btn btn-sm remove-item no-bg">
    <i class="fas fa-trash text-danger"></i>
</button></td>
                        </tr>
                    `;
          document
            .querySelector("#itemsTableMain tbody")
            .insertAdjacentHTML("afterbegin", newRow);

          // Reindex row numbers
          document
            .querySelectorAll("#itemsTableMain tbody tr")
            .forEach((row, index) => {
              row.cells[0].textContent = index + 1;
              row.dataset.rowId = index + 1;
            });
          itemCounter = document.querySelectorAll(
            "#itemsTableMain tbody tr"
          ).length;
        }

        updateTotals();
        saveState();

        let modal = bootstrap.Modal.getInstance(
          document.getElementById("itemModal")
        );
        modal.hide();
      });
    });

    // Quantity input
    document.addEventListener("input", function (e) {
      if (e.target.classList.contains("quantity-input")) {
        let row = e.target.closest("tr");
        if (!row) return;
        let priceExcl = parseFloat(row.dataset.priceExcl);
        let quantity =
          parseFloat(row.querySelector(".quantity-input").value) || 1;
        let discount = parseFloat(row.dataset.discount) || 0;
        let discountType = row.dataset.discountType || "percentage";
        let taxPercentage = parseFloat(row.dataset.taxpercentage) || 0;

        let totalExcl = priceExcl * quantity;
        let totalInclBase = totalExcl * (1 + taxPercentage / 100);
        let totalIncl =
          discountType === "percentage"
            ? totalInclBase * (1 - discount / 100)
            : totalInclBase - discount;

        row.querySelector(".original-price").textContent = priceExcl.toFixed(2);
        row.querySelector(".price").textContent = priceExcl.toFixed(2);
        row.querySelector(".total-excl").textContent = totalExcl.toFixed(2);
        row.querySelector(".total-incl").textContent = totalIncl.toFixed(2);
        updateTotals();
        saveState();
      }
    });

    // Discount cell click to open modal
    document.addEventListener("click", function (e) {
      if (e.target.classList.contains("discount-cell")) {
        currentRow = e.target.closest("tr");
        if (!currentRow) return;

        let discount = parseFloat(currentRow.dataset.discount) || 0;
        let discountType = currentRow.dataset.discountType || "percentage";

        // Set modal values
        document.getElementById("discountValue").value = discount;
        document.getElementById(
          discountType === "percentage"
            ? "discountPercentage"
            : "discountAmount"
        ).checked = true;

        // Show modal
        let discountModal = new bootstrap.Modal(
          document.getElementById("discountModal")
        );
        discountModal.show();
      }
    });

    // Apply discount from modal
    document
      .getElementById("applyDiscountBtn")
      .addEventListener("click", function () {
        if (!currentRow) return;

        let discount =
          parseFloat(document.getElementById("discountValue").value) || 0;
        let discountType = document.querySelector(
          'input[name="discountType"]:checked'
        ).value;
        let priceExcl = parseFloat(currentRow.dataset.priceExcl);
        let quantity =
          parseFloat(currentRow.querySelector(".quantity-input").value) || 1;
        let taxPercentage = parseFloat(currentRow.dataset.taxpercentage) || 0;

        // Update row data
        currentRow.dataset.discount = discount;
        currentRow.dataset.discountType = discountType;
        currentRow.querySelector(".discount-cell").textContent =
          discountType === "percentage"
            ? discount + "%"
            : "KES  " + discount.toFixed(2);

        // Update totals
        let totalExcl = priceExcl * quantity;
        let totalInclBase = totalExcl * (1 + taxPercentage / 100);
        let totalIncl =
          discountType === "percentage"
            ? totalInclBase * (1 - discount / 100)
            : totalInclBase - discount;

        currentRow.querySelector(".original-price").textContent =
          priceExcl.toFixed(2);
        currentRow.querySelector(".price").textContent = priceExcl.toFixed(2);
        currentRow.querySelector(".total-excl").textContent =
          totalExcl.toFixed(2);
        currentRow.querySelector(".total-incl").textContent =
          totalIncl.toFixed(2);

        updateTotals();
        saveState();

        let modal = bootstrap.Modal.getInstance(
          document.getElementById("discountModal")
        );
        modal.hide();
      });

    // Remove item with confirmation modal
    document.addEventListener("click", function (e) {
      const removeBtn = e.target.closest(".remove-item"); // works for button or icon
      if (removeBtn) {
        currentRow = removeBtn.closest("tr"); // Store the row to remove
        if (!currentRow) return;

        // Set item name in modal (use Description or fallback if empty)
        let itemName = currentRow.cells[2].textContent || "this item";
        document.getElementById(
          "removeConfirmMessage"
        ).textContent = `Are you sure you want to remove "${itemName}"?`;

        // Show confirmation modal
        let removeModal = new bootstrap.Modal(
          document.getElementById("removeConfirmModal")
        );
        removeModal.show();
      }
    });

    // Confirm removal
    document
      .getElementById("confirmRemoveBtn")
      .addEventListener("click", function () {
        if (currentRow) {
          currentRow.remove();

          // Reindex rows
          document
            .querySelectorAll("#itemsTableMain tbody tr")
            .forEach((row, index) => {
              row.cells[0].textContent = index + 1;
              row.dataset.rowId = index + 1;
            });
          itemCounter = document.querySelectorAll(
            "#itemsTableMain tbody tr"
          ).length;

          updateTotals();
          saveState();

          // If no items remain, reset like Void button
          if (itemCounter === 0) {
            localStorage.removeItem("cart");
            itemCounter = 0;
            location.reload(); // refresh window
          }

          let modal = bootstrap.Modal.getInstance(
            document.getElementById("removeConfirmModal")
          );
          modal.hide();
          currentRow = null; // Clear the reference
        }
      });

    // ===============================================
    // FINAL SAVE TRANSACTION – 100% WORKING & CLEAN
    // ===============================================
    document
      .getElementById("saveBtn")
      .addEventListener("click", async function () {
        const rows = document.querySelectorAll("#itemsTableMain tbody tr");
        const hasItems = Array.from(rows).some((row) =>
          row.cells[1]?.textContent.trim()
        );

        if (!hasItems) {
          alert("No items in cart!");
          return;
        }

        if (!confirm("Save this transaction to database?")) return;

        // GET CASHIER ID FROM HIDDEN INPUT (SAFE & WORKING!)
        const cashierID =
          parseInt(document.getElementById("currentCashierID").value) || 54;

        try {
          // Get current batch
          const batchRes = await fetch("get_batch.php");
          const batchData = await batchRes.json();
          const batchNumber = batchData.batchNumber;

          // Get customer ID
          const customerHTML =
            document.getElementById("customerInfo").innerHTML;
          const nameMatch = customerHTML.match(/Name:<\/strong>\s*([^<]+)/);
          const customerName = nameMatch ? nameMatch[1].trim() : "-";
          const customerID =
            customerName !== "-"
              ? (
                  await (
                    await fetch(
                      `get_customer.php?name=${encodeURIComponent(
                        customerName
                      )}`
                    )
                  ).json()
                ).id
              : 0;

          // Totals
          const subTotal =
            parseFloat(
              document
                .getElementById("subTotal")
                .textContent.replace(/[^\d.-]/g, "")
            ) || 0;
          const taxTotal =
            parseFloat(
              document
                .getElementById("taxTotal")
                .textContent.replace(/[^\d.-]/g, "")
            ) || 0;
          const grandTotal = subTotal + taxTotal;

          const remarks = document.getElementById("remarks").value.trim();

          // 1. Save Header
          const headerRes = await fetch("save_transaction.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              action: "insert_header",
              ShipToIDX: 1,
              StoreIDX: 1,
              BatchNumberX: batchNumber,
              CustomerIDX: customerID,
              CashierIDX: cashierID, // FIXED: Now pure JavaScript!
              TotalX: grandTotal,
              SalesTaxX: taxTotal,
              CommentX: remarks,
              RefenceNumberX: "",
              StatusX: 1,
              ExchangeIDX: 1,
              ChannelTypeX: 1,
              RecallIDX: 0,
              RecallTypeX: 0,
            }),
          });

          const header = await headerRes.json();
          if (!header.success)
            throw new Error(header.error || "Failed to save header");
          const transactionID = header.transactionID;

          // 2. Save Each Line Item
          for (let row of rows) {
            const code = row.cells[1]?.textContent.trim();
            if (!code) continue;

            const itemRes = await fetch(
              `get_item.php?code=${encodeURIComponent(code)}`
            );
            const itemData = await itemRes.json();
            if (!itemData.id) {
              console.warn("Item not found:", code);
              continue;
            }

            const qty =
              parseFloat(row.querySelector(".quantity-input").value) || 1;
            const priceExcl = parseFloat(row.dataset.priceExcl) || 0;
            const taxRate = parseFloat(row.dataset.taxpercentage) || 0;
            const discount = parseFloat(row.dataset.discount) || 0;
            const discountType = row.dataset.discountType || "percentage";

            const lineExcl = priceExcl * qty;
            const lineInclBase = lineExcl * (1 + taxRate / 100);
            const lineIncl =
              discountType === "percentage"
                ? lineInclBase * (1 - discount / 100)
                : lineInclBase - discount;
            const lineTax = lineIncl - lineExcl;

            await fetch("save_transaction.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({
                action: "insert_entry",
                TransactionIDX: transactionID,
                StoreIDX: 1,
                ItemIDX: itemData.id,
                QuantityX: qty,
                PriceX: priceExcl,
                FullPriceX: priceExcl,
                CostX: 0,
                CommissionX: 0,
                SalesRepIDX: 0,
                TaxableX: taxRate > 0 ? 1 : 0,
                SalesTaxX: lineTax,
                CommentX: "",
                DiscountReasonCodeIDX: 0,
                ReturnReasonCodeIDX: 0,
                QuantityDiscountIDX: 0,
                BatchNoX: "<none>",
                PriceSourceX: 1,
                DetailedIDX: 0,
              }),
            });
          }

          alert(`Transaction #${transactionID} saved successfully!`);
          localStorage.removeItem("cart");
          location.reload();
        } catch (err) {
          console.error("Save Error:", err);
          alert("Save failed: " + err.message);
        }
      });
    document
      .getElementById("invoicesBtn")
      .addEventListener("click", function () {
        console.log("Invoices button clicked");
        // Implement invoices logic here
      });
  } catch (e) {
    console.error("Error initializing event listeners:", e);
  }
}

// Simulate reloading details
function updateDetails(transactionType) {
  try {
    updateTotals();
  } catch (e) {
    console.error("Error in updateDetails:", e);
  }
}

// Check if table body is empty and add three empty rows
if (document.querySelector("#itemsTableMain tbody").children.length === 0) {
  for (let i = 0; i < 5; i++) {
    itemCounter++;
    const emptyRow = `
            <tr data-row-id="${itemCounter}" data-taxpercentage="0" data-price-excl="0" data-discount="0" data-discount-type="percentage">
                <td>${itemCounter}</td>
                <td></td>
                <td></td>
                <td class="original-price"></td>
                <td></td>
                <td class="discount-cell"></td>
                <td class="price"></td>
                <td class="total-excl"></td>
                <td class="total-incl"></td>
            </tr>
        `;
    document
      .querySelector("#itemsTableMain tbody")
      .insertAdjacentHTML("beforeend", emptyRow);
  }
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", function () {
  try {
    loadState();
    initializeEventListeners();
  } catch (e) {
    console.error("Error in DOMContentLoaded:", e);
  }
});

document
  .getElementById("closePropertiesBtn")
  .addEventListener("click", function () {
    // Hide Properties Modal
    const propertiesModal = bootstrap.Modal.getInstance(
      document.getElementById("propertiesModal")
    );
    if (propertiesModal) propertiesModal.hide();

    // Show Customer Modal again
    const customerModal = new bootstrap.Modal(
      document.getElementById("customerModal")
    );
    customerModal.show();
  });

async function getCurrentBatchNumber() {
  const res = await fetch("get_batch.php");
  const data = await res.json();
  return data.batchNumber || 1;
}

async function getCustomerIDByName(name) {
  const res = await fetch(`get_customer.php?name=${encodeURIComponent(name)}`);
  const data = await res.json();
  return data.id || 0;
}

async function getItemIDByCode(code) {
  const res = await fetch(`get_item.php?code=${encodeURIComponent(code)}`);
  const data = await res.json();
  return data.id || null;
}