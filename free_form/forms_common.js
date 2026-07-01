(function () {
    function getDataUrl() {
        const params = new URLSearchParams(window.location.search);
        return params.get('data') || '../index.php/admin/forms-data';
    }

    function setIfAvailable(input, value) {
        if (!input || value === undefined || value === null || value === '') {
            return;
        }

        if (!input.value.trim() || input.dataset.formsDefaultApplied === '1') {
            input.value = value;
            input.dataset.formsDefaultApplied = '1';
        }
    }

    function injectEmployeeDropdownStyles() {
        if (document.getElementById('formsEmployeeDropdownStyles')) {
            return;
        }

        const style = document.createElement('style');
        style.id = 'formsEmployeeDropdownStyles';
        style.textContent = `
            .forms-employee-dropdown {
                position: absolute;
                z-index: 99999;
                display: none;
                max-height: 260px;
                overflow: auto;
                border: 1px solid rgba(102, 126, 234, 0.26);
                border-radius: 14px;
                background: #fff;
                box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
                direction: rtl;
                font-family: inherit;
            }

            .forms-employee-dropdown.is-open {
                display: block;
            }

            .forms-employee-option,
            .forms-employee-empty {
                width: 100%;
                min-height: 44px;
                padding: 10px 13px;
                border: 0;
                border-bottom: 1px solid #eef2f7;
                background: #fff;
                color: #1f2937;
                font: inherit;
                text-align: right;
            }

            .forms-employee-option {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 10px;
                align-items: center;
                cursor: pointer;
            }

            .forms-employee-option:last-child,
            .forms-employee-empty:last-child {
                border-bottom: 0;
            }

            .forms-employee-option:hover,
            .forms-employee-option.is-active {
                background: linear-gradient(135deg, #f7f8ff 0%, #eef2ff 100%);
                color: #4f46e5;
            }

            .forms-employee-name {
                overflow: hidden;
                font-size: 14px;
                font-weight: 800;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .forms-employee-number {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 72px;
                min-height: 26px;
                padding: 4px 8px;
                border-radius: 999px;
                background: #eef2ff;
                color: #4f46e5;
                font-size: 12px;
                font-weight: 900;
                direction: ltr;
            }

            .forms-employee-empty {
                color: #64748b;
                font-size: 13px;
                font-weight: 800;
            }

            @media print {
                .forms-employee-dropdown {
                    display: none !important;
                }
            }
        `;
        document.head.appendChild(style);
    }

    function getEmployeeDropdown() {
        let dropdown = document.getElementById('formsEmployeeDropdown');
        if (!dropdown) {
            dropdown = document.createElement('div');
            dropdown.id = 'formsEmployeeDropdown';
            dropdown.className = 'forms-employee-dropdown';
            document.body.appendChild(dropdown);
        }

        return dropdown;
    }

    function positionEmployeeDropdown(input, dropdown) {
        const rect = input.getBoundingClientRect();
        dropdown.style.top = `${rect.bottom + window.scrollY + 6}px`;
        dropdown.style.left = `${rect.left + window.scrollX}px`;
        dropdown.style.width = `${rect.width}px`;
    }

    function attachEmployeePicker(nameInput, idInput, employees, signatureInput) {
        if (!nameInput || !idInput) {
            return;
        }

        injectEmployeeDropdownStyles();
        const dropdown = getEmployeeDropdown();
        nameInput.removeAttribute('list');
        nameInput.setAttribute('autocomplete', 'off');

        // START settlement employee signature sync additions
        const syncEmployeeSignature = (employeeName, forceSync) => {
            if (!signatureInput) {
                return;
            }

            if (forceSync || !signatureInput.value.trim() || signatureInput.dataset.syncedFromEmployeeName === '1') {
                signatureInput.value = employeeName || '';
                signatureInput.dataset.syncedFromEmployeeName = '1';
            }
        };

        if (signatureInput) {
            signatureInput.addEventListener('input', () => {
                signatureInput.dataset.syncedFromEmployeeName = signatureInput.value === nameInput.value ? '1' : '0';
            });
        }
        // END settlement employee signature sync additions

        const selectEmployee = (employee) => {
            nameInput.value = employee.name || '';
            idInput.value = employee.employee_number || '';
            // START settlement employee signature sync additions
            syncEmployeeSignature(employee.name || '', true);
            // END settlement employee signature sync additions
            // START settlement empty field color additions
            document.dispatchEvent(new CustomEvent('forms:writable-fields-updated'));
            // END settlement empty field color additions
            dropdown.classList.remove('is-open');
        };

        const filteredEmployees = () => {
            const search = nameInput.value.trim().toLowerCase();
            if (!search) {
                return employees.slice(0, 10);
            }

            return employees.filter((employee) => {
                const name = (employee.name || '').toLowerCase();
                const number = (employee.employee_number || '').toLowerCase();
                return name.indexOf(search) !== -1 || number.indexOf(search) !== -1;
            }).slice(0, 10);
        };

        const renderDropdown = () => {
            const results = filteredEmployees();
            dropdown.innerHTML = '';
            positionEmployeeDropdown(nameInput, dropdown);

            if (!results.length) {
                const empty = document.createElement('div');
                empty.className = 'forms-employee-empty';
                empty.textContent = 'لا توجد نتائج';
                dropdown.appendChild(empty);
            } else {
                results.forEach((employee) => {
                    const option = document.createElement('button');
                    option.type = 'button';
                    option.className = 'forms-employee-option';
                    option.innerHTML = `<span class="forms-employee-name"></span><span class="forms-employee-number"></span>`;
                    option.querySelector('.forms-employee-name').textContent = employee.name || '';
                    option.querySelector('.forms-employee-number').textContent = employee.employee_number || 'بدون رقم';
                    option.addEventListener('mousedown', (event) => {
                        event.preventDefault();
                        selectEmployee(employee);
                    });
                    dropdown.appendChild(option);
                });
            }

            dropdown.classList.add('is-open');
        };

        const fillEmployeeNumber = () => {
            const selectedName = nameInput.value.trim();
            const selectedEmployee = employees.find((employee) => employee.name === selectedName);
            if (selectedEmployee) {
                idInput.value = selectedEmployee.employee_number || '';
                // START settlement employee signature sync additions
                syncEmployeeSignature(selectedEmployee.name || '');
                // END settlement employee signature sync additions
                // START settlement empty field color additions
                document.dispatchEvent(new CustomEvent('forms:writable-fields-updated'));
                // END settlement empty field color additions
            }
        };

        nameInput.addEventListener('focus', renderDropdown);
        nameInput.addEventListener('input', () => {
            fillEmployeeNumber();
            renderDropdown();
        });
        nameInput.addEventListener('change', fillEmployeeNumber);
        window.addEventListener('resize', () => positionEmployeeDropdown(nameInput, dropdown));
        window.addEventListener('scroll', () => positionEmployeeDropdown(nameInput, dropdown), true);

        document.addEventListener('mousedown', (event) => {
            if (event.target === nameInput || dropdown.contains(event.target)) {
                return;
            }

            dropdown.classList.remove('is-open');
        });

        fillEmployeeNumber();
    }

    function applyEmployeePickers(employees) {
        const signatureInputs = Array.from(document.querySelectorAll('.signature-table input[type="text"]'));

        attachEmployeePicker(
            document.querySelector('input[name="employeeName"]'),
            document.querySelector('input[name="employeeId"]'),
            employees,
            null
        );

        attachEmployeePicker(
            document.querySelector('[data-form-employee-name]') || signatureInputs[0],
            document.querySelector('[data-form-employee-id]') || signatureInputs[1],
            employees,
            document.querySelector('[data-form-employee-signature]') || signatureInputs[2]
        );
    }

    function applySettings(settings) {
        setIfAvailable(document.querySelector('input[name="storeManager"]'), settings.manager_name);
        setIfAvailable(document.querySelector('input[name="managerSignatures"]'), settings.manager_name);
        setIfAvailable(document.querySelector('input[name="storeManagerId"]'), settings.manager_employee_id);
        setIfAvailable(document.querySelector('input[name="storeStamp"]'), settings.stamp);
        setIfAvailable(document.querySelector('input[name="storeName"]'), settings.store_name);
        // START settlement form defaults additions
        setIfAvailable(document.querySelector('[data-form-service-package]'), settings.settlement_service_package);
        setIfAvailable(document.querySelector('[data-form-contract-duration]'), settings.settlement_contract_duration);
        // END settlement form defaults additions

        const signatureInputs = Array.from(document.querySelectorAll('.signature-table input[type="text"]'));
        setIfAvailable(document.querySelector('[data-form-manager-name]') || signatureInputs[4], settings.manager_name);
        setIfAvailable(document.querySelector('[data-form-manager-signature]') || signatureInputs[6], settings.manager_name);
        setIfAvailable(document.querySelector('[data-form-manager-id]') || signatureInputs[5], settings.manager_employee_id);
        setIfAvailable(document.querySelector('[data-form-stamp]') || signatureInputs[9], settings.stamp);
        setIfAvailable(document.querySelector('[data-form-store-name]') || signatureInputs[8], settings.store_name);
    }

    function bindMirrorField(sourceSelector, targetSelector) {
        const source = document.querySelector(sourceSelector);
        const target = document.querySelector(targetSelector);

        if (!source || !target) {
            return;
        }

        const sync = () => {
            if (!target.value.trim() || target.dataset.syncedFromSource === '1') {
                target.value = source.value;
                target.dataset.syncedFromSource = '1';
            }
        };

        target.addEventListener('input', () => {
            target.dataset.syncedFromSource = target.value === source.value ? '1' : '0';
        });
        source.addEventListener('input', sync);
        source.addEventListener('change', sync);
        sync();
    }

    function applyReplacementMirrors() {
        bindMirrorField('input[name="model"]', 'input[name="newModel"]');
        bindMirrorField('input[name="color"]', 'input[name="newColor"]');
    }

    document.addEventListener('DOMContentLoaded', function () {
        applyReplacementMirrors();

        fetch(getDataUrl(), { credentials: 'same-origin' })
            .then((response) => response.ok ? response.json() : null)
            .then((payload) => {
                if (!payload || payload.status !== 'ok') {
                    return;
                }

                applySettings(payload.settings || {});
                applyEmployeePickers(payload.employees || []);
            })
            .catch(() => {});
    });
})();
