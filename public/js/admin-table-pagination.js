(function () {
  var instances = [];
  var labels = {
    previous: "\u0627\u0644\u0633\u0627\u0628\u0642",
    next: "\u0627\u0644\u062a\u0627\u0644\u064a",
    showing: "\u0639\u0631\u0636",
    from: "\u0645\u0646",
    noResults: "\u0644\u0627 \u062a\u0648\u062c\u062f \u0646\u062a\u0627\u0626\u062c"
  };

  function normalize(value) {
    return (value || "").toString().toLowerCase();
  }

  function getRows(instance) {
    return Array.prototype.slice.call(instance.table.querySelectorAll(".products-row"));
  }

  function getRowSearchText(row) {
    var chunks = [row.textContent || ""];

    Array.prototype.slice.call(row.attributes).forEach(function (attribute) {
      if (attribute.name.indexOf("data-") === 0) {
        chunks.push(attribute.value || "");
      }
    });

    return normalize(chunks.join(" "));
  }

  function getMatchingRows(instance) {
    var query = normalize(instance.searchInput ? instance.searchInput.value : "").trim();
    var rows = getRows(instance);

    if (!query) {
      return rows;
    }

    return rows.filter(function (row) {
      return getRowSearchText(row).indexOf(query) !== -1;
    });
  }

  function addButton(container, text, page, isActive, isDisabled, label) {
    var button = document.createElement("button");
    button.type = "button";
    button.className = "admin-page-btn";
    button.textContent = text;
    button.setAttribute("data-admin-page", page);

    if (label) {
      button.setAttribute("aria-label", label);
      button.title = label;
    }

    if (isActive) {
      button.classList.add("is-active");
      button.setAttribute("data-active", "true");
      button.setAttribute("aria-current", "page");
    }

    if (isDisabled) {
      button.disabled = true;
    }

    container.appendChild(button);
  }

  function addEllipsis(container) {
    var ellipsis = document.createElement("span");
    ellipsis.className = "admin-page-ellipsis";
    ellipsis.textContent = "...";
    container.appendChild(ellipsis);
  }

  function getVisiblePages(currentPage, totalPages) {
    var pages = [];

    function addPage(page) {
      if (page >= 1 && page <= totalPages && pages.indexOf(page) === -1) {
        pages.push(page);
      }
    }

    addPage(1);
    addPage(totalPages);

    for (var page = currentPage - 2; page <= currentPage + 2; page += 1) {
      addPage(page);
    }

    return pages.sort(function (a, b) {
      return a - b;
    });
  }

  function renderPagination(instance, totalRows, totalPages, startIndex, endIndex) {
    instance.container.innerHTML = "";

    var info = document.createElement("div");
    info.className = "admin-pagination-info";

    if (totalRows === 0) {
      info.textContent = labels.noResults;
      instance.container.appendChild(info);
      return;
    }

    info.textContent = labels.showing + " " + (startIndex + 1) + " - " + endIndex + " " + labels.from + " " + totalRows;
    instance.container.appendChild(info);

    if (totalPages <= 1) {
      return;
    }

    var pages = document.createElement("div");
    pages.className = "admin-pagination-pages";

    addButton(
      pages,
      labels.previous,
      Math.max(1, instance.currentPage - 1),
      false,
      instance.currentPage <= 1,
      labels.previous
    );

    var visiblePages = getVisiblePages(instance.currentPage, totalPages);
    visiblePages.forEach(function (page, index) {
      if (index > 0 && page - visiblePages[index - 1] > 1) {
        addEllipsis(pages);
      }

      addButton(pages, page, page, page === instance.currentPage, false, labels.showing + " " + page);
    });

    addButton(
      pages,
      labels.next,
      Math.min(totalPages, instance.currentPage + 1),
      false,
      instance.currentPage >= totalPages,
      labels.next
    );

    instance.container.appendChild(pages);
  }

  function render(instance) {
    var rows = getRows(instance);
    var matchingRows = getMatchingRows(instance);
    var totalRows = matchingRows.length;
    var totalPages = Math.max(1, Math.ceil(totalRows / instance.pageSize));

    if (instance.currentPage > totalPages) {
      instance.currentPage = totalPages;
    }

    var startIndex = (instance.currentPage - 1) * instance.pageSize;
    var endIndex = Math.min(startIndex + instance.pageSize, totalRows);
    var visibleRows = matchingRows.slice(startIndex, endIndex);

    rows.forEach(function (row) {
      row.style.display = "none";
    });

    visibleRows.forEach(function (row) {
      row.style.display = "";
    });

    renderPagination(instance, totalRows, totalPages, startIndex, endIndex);
  }

  function updateFromInput(input) {
    var matched = false;

    instances.forEach(function (instance) {
      if (instance.searchInput === input) {
        instance.currentPage = 1;
        render(instance);
        matched = true;
      }
    });

    return matched;
  }

  function refresh(target) {
    instances.forEach(function (instance) {
      if (!target || instance.table === target || instance.container === target) {
        render(instance);
      }
    });
  }

  function register(container) {
    var targetSelector = container.getAttribute("data-target");
    var searchSelector = container.getAttribute("data-search");
    var table = targetSelector ? document.querySelector(targetSelector) : container.previousElementSibling;
    var pageSize = parseInt(container.getAttribute("data-page-size"), 10) || 20;
    var searchInput = searchSelector ? document.querySelector(searchSelector) : null;

    if (!table) {
      return;
    }

    var instance = {
      container: container,
      table: table,
      pageSize: pageSize,
      searchInput: searchInput,
      currentPage: 1
    };

    instances.push(instance);

    if (searchInput) {
      searchInput.addEventListener("input", function () {
        updateFromInput(searchInput);
      });
    }

    container.addEventListener("click", function (event) {
      var button = event.target.closest("[data-admin-page]");

      if (!button || button.disabled) {
        return;
      }

      instance.currentPage = parseInt(button.getAttribute("data-admin-page"), 10) || 1;
      render(instance);
    });

    render(instance);
  }

  function init() {
    Array.prototype.slice.call(document.querySelectorAll("[data-admin-table-pagination]")).forEach(register);
  }

  window.AdminTablePagination = {
    init: init,
    refresh: refresh,
    updateFromInput: updateFromInput
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
