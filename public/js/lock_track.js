const moneyDenominations = [500, 200, 100, 50, 20, 10, 5, 1, 2, 0.5, 0.25];
const cashTable = document.getElementById("cashTable");

moneyDenominations.forEach((denomination) => {
  const row = document.createElement("tr");

  const numberCell = document.createElement("td");
  const denominationCell = document.createElement("td");
  const totalCell = document.createElement("td");

  numberCell.style.width = "35%";
  denominationCell.style.width = "31%";
  totalCell.style.width = "35%";
  numberCell.style.verticalAlign = "middle";
  denominationCell.style.verticalAlign = "middle";
  totalCell.style.verticalAlign = "middle";
  
[numberCell, denominationCell, totalCell].forEach((cell) => {
  cell.style.verticalAlign = "middle";
  cell.style.padding = "1px";
  cell.style.lineHeight = "2";
});


  const inputField = document.createElement("input");
  inputField.type = "number";
  inputField.classList.add("form-control", "w-100",  "text-center");
  inputField.value = 0;

  inputField.addEventListener("focus", function () {
    this.select();
  });

  inputField.addEventListener("wheel", function (e) {
    this.blur();
    e.preventDefault();
  });

  inputField.addEventListener("keydown", function (e) {
    if (e.key === "ArrowUp" || e.key === "ArrowDown") {
      e.preventDefault();
    }
  });

  inputField.addEventListener("input", function () {
    if (this.value === "") {
      this.value = 0;
    }
    updateRowTotal(row, denomination);
    updateTotalAmount();
  });

  numberCell.appendChild(inputField);
  denominationCell.innerText = `${denomination}`;
  totalCell.innerText = "0.00";

  row.appendChild(numberCell);
  row.appendChild(denominationCell);
  row.appendChild(totalCell);

  cashTable.appendChild(row);
});

function updateRowTotal(row, denomination) {
  const numberCell = row.children[0].children[0];
  const totalCell = row.children[2];
  const quantity = parseFloat(numberCell.value) || 0;
  const rowTotal = denomination * quantity;

  totalCell.innerText = rowTotal.toFixed(2);
}

function updateTotalAmount() {
  let total = 0;
  const totals = cashTable.querySelectorAll("tr td:nth-child(3)");
  totals.forEach((cell) => {
    total += parseFloat(cell.innerText) || 0;
  });
  document.getElementById("totalAmount").innerText = total.toFixed(2);
}

document
  .getElementById("openCalculatorPopup")
  .addEventListener("click", function () {
    const cashCalculatorModal = new bootstrap.Modal(
      document.getElementById("cashCalculatorModal")
    );
    cashCalculatorModal.show();

    document
      .getElementById("cashCalculatorModal")
      .addEventListener("shown.bs.modal", function () {
        document.getElementById("user_lock_cash").focus();
      });
  });

function submitCash() {
  const total = document.getElementById("totalAmount").innerText;
  document.getElementById("user_lock_cash").value = total;

  updateOverallTotal();

  const cashCalculatorModal = bootstrap.Modal.getInstance(
    document.getElementById("cashCalculatorModal")
  );
  cashCalculatorModal.hide();

  const exampleModal = bootstrap.Modal.getInstance(
    document.getElementById("exampleModal")
  );
  exampleModal.show();
  document.getElementById("exampleModal").focus();
}

function updateOverallTotal() {
  const cash = parseFloat(document.getElementById("user_lock_cash").value) || 0;
  const card = parseFloat(document.getElementById("user_lock_span").value) || 0;
  document.getElementById("totalAmountField").value = (cash + card).toFixed(2);
}

document
  .getElementById("user_lock_cash")
  .addEventListener("input", updateOverallTotal);
document
  .getElementById("user_lock_span")
  .addEventListener("input", updateOverallTotal);

const amountInput = document.getElementById("amountInput");
const repetitionInput = document.getElementById('repetitionInput')
const amountList = document.getElementById("amountList");
const amountCount = document.getElementById("amountCount");
const totalAmount = document.getElementById("totalAmount1");
const amounts = [];

function addAmount() {
  const value = amountInput.value.trim();
  let repetition = repetitionInput.value.trim();
  if (value === "") return;
  if (repetition === "") {
    repetition = 1;
  } else {
    repetition = parseInt(repetition);
  }

  for (let i = 0; i < repetition; i++) {
    amounts.push(value);
  }
  
  renderAmounts();
  amountInput.value = "";
  repetitionInput.value = "";
  amountInput.focus();
}

function removeAmount(index) {
  amounts.splice(index, 1);
  renderAmounts();
}

function renderAmounts() {
  amountList.innerHTML = "";
  let totalAmountValue = 0;

  // Reverse the order when displaying
  [...amounts].reverse().forEach((amount, index) => {
    const number = parseFloat(amount);
    if (!isNaN(number)) totalAmountValue += number;

    const row = document.createElement("tr");
    row.innerHTML = `
  <td style="width: 10%; padding: 2px; line-height: 1; vertical-align: middle;">
    ${amounts.length - index}
  </td>
  <td style="width: 70%; padding: 2px; line-height: 1; vertical-align: middle;">
    ${amount}
  </td>
  <td style="width: 20%; padding: 2px; line-height: 1; vertical-align: middle;">
    <button class="btn btn-danger btn-sm" onclick="removeAmount(${
      amounts.length - 1 - index
    })">🗑</button>
  </td>
`;


    // Add hover effect
    row.addEventListener("mouseover", () => {
      row.style.backgroundColor = "#f0f0f0";
    });
    row.addEventListener("mouseout", () => {
      row.style.backgroundColor = "";
    });

    amountList.appendChild(row);
  });

  amountCount.textContent = amounts.length;
  totalAmount.textContent = totalAmountValue.toFixed(2);
}

function esc() {
  const amountModalEl = document.getElementById("amountModal");
  const amountModal = bootstrap.Modal.getInstance(amountModalEl);

  if (amountModal) {
    const totalAmountValue =
      document.getElementById("totalAmount1").textContent;
    document.getElementById("user_lock_span").value = totalAmountValue;
    amountModal.hide();

    updateOverallTotal();
  }
}

amountInput.addEventListener("keydown", function (e) {
  if (e.key === "Enter") {
    addAmount();
  }
});

document.getElementById("Approval").addEventListener("click", function () {
  const totalAmountValue = document.getElementById("totalAmount1").innerText;

  if (totalAmountValue !== "0" && totalAmountValue !== "") {
    document.getElementById("user_lock_span").value = totalAmountValue;

    const amountModal = new bootstrap.Modal(
      document.getElementById("amountModal")
    );
    amountModal.hide();

    const amountModalInstance = bootstrap.Modal.getInstance(
      document.getElementById("amountModal")
    );
    if (amountModalInstance) {
      amountModalInstance.hide();
    }

    const exampleModal = new bootstrap.Modal(document.getElementById("exampleModal"), {
      backdrop: "static",
      keyboard: false,
    });
    exampleModal.show();
  } else {
    alert("الرجاء التأكد من إدخال المبلغ قبل الاعتماد.");
  }
});
document.addEventListener("keydown", function (e) {
  const isArrowUp = e.key === "ArrowUp";
  const isArrowDown = e.key === "ArrowDown" || e.key === "Enter";

  if (isArrowUp || isArrowDown) {
    const active = document.activeElement;
    if (active.tagName === "INPUT") {
      const inputs = Array.from(document.querySelectorAll("#cashTable input"));
      const currentIndex = inputs.indexOf(active);
      const nextIndex = isArrowDown ? currentIndex + 1 : currentIndex - 1;

      if (inputs[nextIndex]) {
        inputs[nextIndex].focus();
        e.preventDefault();
      }
    }
  }
});

$(document).ready(function () {
  $("#user_id").select2({
    placeholder: "اختر",
    width: "resolve",
    dir: "rtl",
    language: "ar",
    minimumInputLength: 0,
    minimumResultsForSearch: 0,
    dropdownParent: $("#exampleModal"),
  });
});