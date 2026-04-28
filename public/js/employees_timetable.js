const days = ["الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"];
let employees = [];
let supervisors = []; // قائمة المشرفين
let selectedMonth = parseInt(localStorage.getItem('selectedMonth')) || 0;
let selectedYear = parseInt(localStorage.getItem('selectedYear')) || new Date().getFullYear();
let selectedWeek = localStorage.getItem('selectedWeek') || '0';
let currentCell = null;
let scheduleData = {};
let supervisorsData = {}; // بيانات المشرفين

function get_employees() {
  $.get('get_employees', function(data) {
    for (let emp of data) {
      employees.push({name: emp.user_fillname, id: emp.user_id});
    }
  }, 'json');
}

function get_supervisors() {
  $.get('get_supervisors', function(data) {
    for (let supervisor of data) {
      supervisors.push({name : supervisor.user_fillname, id: supervisor.user_id});
    }
  }, 'json');
}

function get_schedule() {
  $.get('get_schedule', function(data) {
    scheduleData = JSON.parse(data);
  });
}

function get_supervisors_schedule() {
  $.get('get_supervisors_schedule', function(data) {
    supervisorsData = JSON.parse(data); // افترض أن البيانات تأتي بنفس التنسيق
  });
}
// إضافة متغير للتحكم في عرض جميع الأسابيع
let showAllWeeks = false;

// إضافة متغير للزر
let downloadBtn;

// دالة لحفظ البيانات في localStorage
function saveScheduleData() {
  $.post('save_user_schedule', { scheduleData: JSON.stringify(scheduleData) }, function(response) {
    
  });

}

// دالة لحفظ بيانات المشرفين في localStorage
function saveSupervisorsData() {
  $.post('save_user_schedule', { scheduleData: JSON.stringify(supervisorsData) }, function(response) {
  });
}

// دالة لاسترجاع بيانات المشرفين من localStorage
function loadSupervisorsData() {
  const savedData = localStorage.getItem('supervisorsData');
  if (savedData) {
    supervisorsData = JSON.parse(savedData);
  }
}

// تحميل البيانات عند بدء التطبيق
get_employees();
get_supervisors();
get_schedule();
get_supervisors_schedule();

const getWeekDates = (month, weekIndex) => {
  const year = selectedYear;
  const firstDay = new Date(year, month, 1);
  const firstSundayOffset = (7 - firstDay.getDay()) % 7;
  const startDate = new Date(year, month, 1 + firstSundayOffset + (weekIndex * 7));

  const dates = [];
  for (let i = 0; i < 7; i++) {
    const day = new Date(startDate);
    day.setDate(startDate.getDate() + i);
    dates.push(day);
  }
  return dates;
};

function formatDate(date) {
  return String(date.getDate()).padStart(2, '0');
}

// دالة للتحقق من وجود الأسبوع الخامس في الشهر
function checkFifthWeekExists(month) {
  const year = new Date().getFullYear();
  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);
  
  // حساب تاريخ بداية الأسبوع الأول
  const firstSundayOffset = (7 - firstDay.getDay()) % 7;
  const firstWeekStart = new Date(year, month, 1 + firstSundayOffset);
  
  // حساب تاريخ بداية الأسبوع الخامس
  const fifthWeekStart = new Date(firstWeekStart);
  fifthWeekStart.setDate(firstWeekStart.getDate() + (4 * 7));
  
  // إذا كان تاريخ بداية الأسبوع الخامس قبل أو في نفس يوم آخر يوم في الشهر
  // فهذا يعني أن الشهر يحتاج أسبوع خامس
  return fifthWeekStart <= lastDay;
}

// تعديل دالة renderTable
const renderTable = () => {
  const container = document.getElementById("scheduleTableContainer");
  const supervisorsContainer = document.getElementById("supervisorsTableContainer");
  container.innerHTML = '';
  supervisorsContainer.innerHTML = '';

  // التحقق من وجود الأسبوع الخامس في الشهر
  const hasFifthWeek = checkFifthWeekExists(selectedMonth);
  const fifthWeekBtn = document.querySelector('.week-btn[data-week="4"]');
  if (fifthWeekBtn) {
    if (!hasFifthWeek) {
      fifthWeekBtn.style.display = 'none';
      if (selectedWeek === '4') {
        selectedWeek = '0';
        document.querySelector('.week-btn[data-week="0"]').classList.add('active');
        document.querySelectorAll('.week-btn').forEach(btn => {
          if (btn.dataset.week !== '0') btn.classList.remove('active');
        });
      } else {
         // إذا كان الأسبوع المحدد غير الخامس، تأكد من أن زر الأسبوع الخامس مخفي
         if (selectedWeek !== '4'){
             fifthWeekBtn.style.display = 'none';
         }
      }
    } else {
      fifthWeekBtn.style.display = 'inline-block';
    }
  }

  if (selectedWeek === 'all') {
    const weeksGridContainer = document.createElement('div');
    weeksGridContainer.className = 'weeks-grid';
    weeksGridContainer.style.display = 'flex';
    weeksGridContainer.style.flexWrap = 'wrap';
    weeksGridContainer.style.gap = '20px';
    weeksGridContainer.style.justifyContent = 'flex-start';
    
    // عدد الأسابيع المعروضة (4 أو 5 حسب وجود الأسبوع الخامس)
    const totalWeeks = hasFifthWeek ? 5 : 4;
    
    for (let week = 0; week < totalWeeks; week++) {
      const weekWrapper = document.createElement('div');
      weekWrapper.className = 'week-wrapper';
      weekWrapper.style.flex = '1';
      weekWrapper.style.minWidth = '300px';
      weekWrapper.style.maxWidth = 'calc(25% - 20px)';
      
      const weekContainer = document.createElement('div');
      weekContainer.className = 'week-container';
      
      const weekTitle = document.createElement('div');
      weekTitle.className = 'week-title';
      weekTitle.textContent = `الأسبوع ${week + 1}`;
      weekContainer.appendChild(weekTitle);

      const dates = getWeekDates(selectedMonth, week);

      // إنشاء جدول الموظفين للأسبوع الحالي
      let employeesHtml = `
        <table class="table table-bordered table-hover">
          <thead class="table-primary">
            <tr>
              <th class="align-middle text-center table-primary">الموظف</th>
              ${dates.map(date => `
                <th class="vertical-header" id="employee-day${week}-${date.getDay()}">
                  <span class="day-name">${days[date.getDay()]}</span>
                  <span class="date">${formatDate(date)}</span>
                </th>
              `).join('')}
            </tr>
          </thead>
          <tbody>
            ${employees.map(emp => `
              <tr>
                <td class="align-middle text-center table-primary fw-bold">${emp.name}</td>
                ${dates.map(date => {
                  const dateStr = date.toISOString().split('T')[0];
                  const cellData = scheduleData[`${emp.id}_${dateStr}`] || {};
                  let cellClass = 'align-middle text-center';
                  if (cellData.type) {
                    switch(cellData.type) {
                      case 'off': cellClass += ' bg-off'; break;
                      case 'leave': cellClass += ' bg-leave'; break;
                      case 'event': cellClass += ' bg-event'; break;
                      case 'hours8': cellClass += ' bg-hours8'; break;
                      case 'hours14': cellClass += ' bg-hours14'; break;
                      case 'store': cellClass += ' bg-store'; break;
                    }
                  } else {
                    cellClass += ' bg-white';
                  }
                  return `<td class="${cellClass}" style="cursor: pointer" data-date="${date.toISOString()}" data-employee="${emp.name}" data-employeeid="${emp.id}">${cellData.status || ''}</td>`;
                }).join('')}
              </tr>
            `).join('')}
          </tbody>
        </table>
      `;
      weekContainer.innerHTML += employeesHtml;

      // إنشاء جدول توزيع الوقت للأسبوع الحالي
      let timeDistributionHtml = `
        <table class="table table-bordered table-hover mt-3" id="timeDistributionTable">
          <thead class="table-primary">
            <tr>
              <th class="align-middle text-center table-primary">توزيع الوقت</th>
              ${dates.map(date => `
                <th class="vertical-header" id="day${date.getDay() + 1}">
                  <span class="day-name">${days[date.getDay()]}</span>
                  <span class="date">${formatDate(date)}</span>
                </th>
              `).join('')}
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="align-middle text-center bg-white text-dark fw-bold">8</td>
              ${dates.map(date => {
                const dateStr = date.toISOString().split('T')[0];
                let time8Count = 0;
                employees.forEach(emp => {
                  const key = `${emp.id}_${dateStr}`;
                  const cellData = scheduleData[key] || {};
                  if (cellData.type === 'hours8') time8Count++;
                });
                return `<td class="text-center time-8 time-cell bg-hours8">${time8Count}</td>`;
              }).join('')}
            </tr>
            <tr>
              <td class="align-middle text-center bg-white text-dark fw-bold">14</td>
              ${dates.map(date => {
                const dateStr = date.toISOString().split('T')[0];
                let time14Count = 0;
                employees.forEach(emp => {
                  const key = `${emp.id}_${dateStr}`;
                  const cellData = scheduleData[key] || {};
                  if (cellData.type === 'hours14') time14Count++;
                });
                return `<td class="text-center time-14 time-cell bg-hours14">${time14Count}</td>`;
              }).join('')}
            </tr>
            <tr>
              <td class="align-middle text-center bg-leave fw-bold">إجازة</td>
              ${dates.map(date => {
                const dateStr = date.toISOString().split('T')[0];
                let leaveCount = 0;
                employees.forEach(emp => {
                  const key = `${emp.id}_${dateStr}`;
                  const cellData = scheduleData[key] || {};
                  if (cellData.type === 'leave') leaveCount++;
                });
                return `<td class="text-center leave time-cell bg-leave">${leaveCount}</td>`;
              }).join('')}
            </tr>
            <tr>
              <td class="align-middle text-center bg-off fw-bold">OFF</td>
              ${dates.map(date => {
                const dateStr = date.toISOString().split('T')[0];
                let offCount = 0;
                employees.forEach(emp => {
                  const key = `${emp.id}_${dateStr}`;
                  const cellData = scheduleData[key] || {};
                  if (cellData.type === 'off') offCount++;
                });
                return `<td class="text-center off time-cell bg-off">${offCount}</td>`;
              }).join('')}
            </tr>
            <tr>
              <td class="align-middle text-center bg-store fw-bold">ستور</td>
              ${dates.map(date => {
                const dateStr = date.toISOString().split('T')[0];
                let storeCount = 0;
                employees.forEach(emp => {
                  const key = `${emp.id}_${dateStr}`;
                  const cellData = scheduleData[key] || {};
                  if (cellData.type === 'store') storeCount++;
                });
                return `<td class="text-center store time-cell bg-store">${storeCount}</td>`;
              }).join('')}
            </tr>
          </tbody>
        </table>
      `;
      weekContainer.innerHTML += timeDistributionHtml;

      // إنشاء جدول المشرفين للأسبوع الحالي
      let supervisorsHtml = `
        <table class="table table-bordered table-hover mt-3">
          <thead class="table-primary">
            <tr>
              <th class="align-middle text-center table-primary">المشرف</th>
              ${dates.map(date => `
                <th class="vertical-header" id="supervisor-day${week}-${date.getDay()}">
                  <span class="day-name">${days[date.getDay()]}</span>
                  <span class="date">${formatDate(date)}</span>
                </th>
              `).join('')}
            </tr>
          </thead>
          <tbody>
            ${supervisors.map(sup => `
              <tr>
                <td class="align-middle text-center table-primary fw-bold">${sup.name}</td>
                ${dates.map(date => {
                  const dateStr = date.toISOString().split('T')[0];
                  const cellData = supervisorsData[`${sup.id}_${dateStr}`] || {};
                  let cellClass = 'align-middle text-center';
                  if (cellData.type) {
                    switch(cellData.type) {
                      case 'off': cellClass += ' bg-off'; break;
                      case 'leave': cellClass += ' bg-leave'; break;
                      case 'event': cellClass += ' bg-event'; break;
                      case 'hours8': cellClass += ' bg-hours8'; break;
                      case 'hours14': cellClass += ' bg-hours14'; break;
                      case 'store': cellClass += ' bg-store'; break;
                    }
                  } else {
                    cellClass += ' bg-white';
                  }
                  return `<td class="${cellClass}" style="cursor: pointer" data-date="${date.toISOString()}" data-supervisor="${sup.name}" data-supervisorid="${sup.id}">${cellData.status || ''}</td>`;
                }).join('')}
              </tr>
            `).join('')}
          </tbody>
        </table>
      `;
      weekContainer.innerHTML += supervisorsHtml;
      
      weekWrapper.appendChild(weekContainer);
      weeksGridContainer.appendChild(weekWrapper);
    }
    
    container.appendChild(weeksGridContainer);
    // إظهار زر التحميل في عرض الكل
    if (downloadBtn) {
      downloadBtn.style.display = 'inline-block';
    }
  } else {
    // عرض أسبوع واحد فقط
    const dates = getWeekDates(selectedMonth, selectedWeek);
    
    // جدول الموظفين
    let employeesHtml = `
      <table class="table table-bordered table-hover">
        <thead class="table-primary">
          <tr>
            <th class="align-middle text-center table-primary">الموظف</th>
            ${dates.map(date => `
              <th class="vertical-header" id="employee-day${selectedWeek}-${date.getDay()}">
                <span class="day-name">${days[date.getDay()]}</span>
                <span class="date">${formatDate(date)}</span>
              </th>
            `).join('')}
          </tr>
        </thead>
        <tbody>
          ${employees.map(emp => `
            <tr>
              <td class="align-middle text-center table-primary fw-bold">${emp.name}</td>
              ${dates.map(date => {
                const dateStr = date.toISOString().split('T')[0];
                const cellData = scheduleData[`${emp.id}_${dateStr}`] || {};
                let cellClass = 'align-middle text-center';
                if (cellData.type) {
                  switch(cellData.type) {
                    case 'off': cellClass += ' bg-off'; break;
                    case 'leave': cellClass += ' bg-leave'; break;
                    case 'event': cellClass += ' bg-event'; break;
                    case 'hours8': cellClass += ' bg-hours8'; break;
                    case 'hours14': cellClass += ' bg-hours14'; break;
                    case 'store': cellClass += ' bg-store'; break;
                  }
                } else {
                  cellClass += ' bg-white';
                }
                return `<td class="${cellClass}" style="cursor: pointer" data-date="${date.toISOString()}" data-employee="${emp.name}" data-employeeid="${emp.id}">${cellData.status || ''}</td>`;
              }).join('')}
            </tr>
          `).join('')}
        </tbody>
      </table>
    `;
    container.innerHTML = employeesHtml;

    // جدول توزيع الوقت
    let timeDistributionHtml = `
      <table class="table table-bordered table-hover mt-3" id="timeDistributionTable">
        <thead class="table-primary">
          <tr>
            <th class="align-middle text-center table-primary">توزيع الوقت</th>
            ${dates.map(date => `
              <th class="vertical-header" id="day${date.getDay() + 1}">
                <span class="day-name">${days[date.getDay()]}</span>
                <span class="date">${formatDate(date)}</span>
              </th>
            `).join('')}
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="align-middle text-center bg-white text-dark fw-bold">8</td>
            ${dates.map(date => {
              const dateStr = date.toISOString().split('T')[0];
              let time8Count = 0;
              employees.forEach(emp => {
                const key = `${emp.id}_${dateStr}`;
                const cellData = scheduleData[key] || {};
                if (cellData.type === 'hours8') time8Count++;
              });
              return `<td class="text-center time-8 time-cell bg-hours8">${time8Count}</td>`;
            }).join('')}
          </tr>
          <tr>
            <td class="align-middle text-center bg-white text-dark fw-bold">14</td>
            ${dates.map(date => {
              const dateStr = date.toISOString().split('T')[0];
              let time14Count = 0;
              employees.forEach(emp => {
                const key = `${emp.id}_${dateStr}`;
                const cellData = scheduleData[key] || {};
                if (cellData.type === 'hours14') time14Count++;
              });
              return `<td class="text-center time-14 time-cell bg-hours14">${time14Count}</td>`;
            }).join('')}
          </tr>
          <tr>
            <td class="align-middle text-center bg-leave fw-bold">إجازة</td>
            ${dates.map(date => {
              const dateStr = date.toISOString().split('T')[0];
              let leaveCount = 0;
              employees.forEach(emp => {
                const key = `${emp.id}_${dateStr}`;
                const cellData = scheduleData[key] || {};
                if (cellData.type === 'leave') leaveCount++;
              });
              return `<td class="text-center leave time-cell bg-leave">${leaveCount}</td>`;
            }).join('')}
          </tr>
          <tr>
            <td class="align-middle text-center bg-off fw-bold">OFF</td>
            ${dates.map(date => {
              const dateStr = date.toISOString().split('T')[0];
              let offCount = 0;
              employees.forEach(emp => {
                const key = `${emp.id}_${dateStr}`;
                const cellData = scheduleData[key] || {};
                if (cellData.type === 'off') offCount++;
              });
              return `<td class="text-center off time-cell bg-off">${offCount}</td>`;
            }).join('')}
          </tr>
          <tr>
            <td class="align-middle text-center bg-store fw-bold">ستور</td>
            ${dates.map(date => {
              const dateStr = date.toISOString().split('T')[0];
              let storeCount = 0;
              employees.forEach(emp => {
                const key = `${emp.id}_${dateStr}`;
                const cellData = scheduleData[key] || {};
                if (cellData.type === 'store') storeCount++;
              });
              return `<td class="text-center store time-cell bg-store">${storeCount}</td>`;
            }).join('')}
          </tr>
        </tbody>
      </table>
    `;
    container.innerHTML += timeDistributionHtml;

    // جدول المشرفين
    let supervisorsHtml = `
      <table class="table table-bordered table-hover">
        <thead class="table-primary">
          <tr>
            <th class="align-middle text-center table-primary">المشرف</th>
            ${dates.map(date => `
              <th class="vertical-header" id="supervisor-day${selectedWeek}-${date.getDay()}">
                <span class="day-name">${days[date.getDay()]}</span>
                <span class="date">${formatDate(date)}</span>
              </th>
            `).join('')}
          </tr>
        </thead>
        <tbody>
          ${supervisors.map(sup => `
            <tr>
              <td class="align-middle text-center table-primary fw-bold">${sup.name}</td>
              ${dates.map(date => {
                const dateStr = date.toISOString().split('T')[0];
                const cellData = supervisorsData[`${sup.id}_${dateStr}`] || {};
                let cellClass = 'align-middle text-center';
                if (cellData.type) {
                  switch(cellData.type) {
                    case 'off': cellClass += ' bg-off'; break;
                    case 'leave': cellClass += ' bg-leave'; break;
                    case 'event': cellClass += ' bg-event'; break;
                    case 'hours8': cellClass += ' bg-hours8'; break;
                    case 'hours14': cellClass += ' bg-hours14'; break;
                    case 'store': cellClass += ' bg-store'; break;
                  }
                } else {
                  cellClass += ' bg-white';
                }
                return `<td class="${cellClass}" style="cursor: pointer" data-date="${date.toISOString()}" data-supervisor="${sup.name}" data-supervisorid="${sup.id}">${cellData.status || ''}</td>`;
              }).join('')}
            </tr>
          `).join('')}
        </tbody>
      </table>
    `;
    supervisorsContainer.innerHTML = supervisorsHtml;

    // تحديث جدول توزيع الوقت
    updateTimeDistributionTable();
    
    // إخفاء زر التحميل في عرض الأسبوع الواحد
    if (downloadBtn) {
      downloadBtn.style.display = 'none';
    }
  }

  // إضافة مستمعي الأحداث للخلايا
  document.querySelectorAll("#scheduleTableContainer td[data-date]").forEach(cell => {
    cell.addEventListener("click", e => showDropdown(e, cell, 'employee'));
  });

  document.querySelectorAll("#supervisorsTableContainer td[data-date]").forEach(cell => {
    cell.addEventListener("click", e => showDropdown(e, cell, 'supervisor'));
  });

  // إضافة مستمعي الأحداث للخلايا في عرض الكل
  if (selectedWeek === 'all') {
    document.querySelectorAll(".week-container td[data-date]").forEach(cell => {
      cell.addEventListener("click", e => showDropdown(e, cell, 'employee'));
    });
  }

  // إعداد خيارات التحميل وإضافة مستمع الحدث لزر التحميل
  const monthName = document.getElementById('monthSelect').options[document.getElementById('monthSelect').selectedIndex].text;
  const weekText = selectedWeek === 'all' ? 'جميع الأسابيع' : `الأسبوع ${parseInt(selectedWeek) + 1}`;
  
  downloadBtn.onclick = () => {
    // إنشاء عنصر مؤقت يحتوي على جميع الجداول
    const tempContainer = document.createElement('div');
    tempContainer.style.padding = '20px';
    
    // نسخ جدول الموظفين
    const employeesTable = container.cloneNode(true);
    // تعديل رؤوس الجدول لملف PDF: تبديل ترتيب التاريخ واسم اليوم وتصحيح التواريخ
    employeesTable.querySelectorAll('th.vertical-header').forEach(th => {
      const dayNameSpan = th.querySelector('.day-name');
      const dateSpan = th.querySelector('.date');

      if (dayNameSpan && dateSpan) {
        // إعادة ترتيب العناصر لعرض التاريخ فوق اليوم لملف PDF
        const originalDateText = dateSpan.textContent;
        const originalDayNameText = dayNameSpan.textContent;
        th.innerHTML = `<div class="d-flex flex-column align-items-center"><span class="pdf-date">${originalDateText}</span><span class="pdf-day-name">${originalDayNameText}</span></div>`;
      }

      // تصحيح التاريخ الفعلي باستخدام المعرف للحصول على التاريخ الصحيح
      const pdfDateSpan = th.querySelector('.pdf-date');
      if (pdfDateSpan) {
        const parentThId = th.id;
        const parts = parentThId.split('-');
        if (parts.length >= 3) {
          const dayIndex = parseInt(parts[parts.length - 1]);
          let weekIndex = 0;
          if (parentThId.startsWith('employee-day')) {
            weekIndex = parseInt(parts[0].replace('employee-day', ''));
          }

          if (!isNaN(dayIndex) && !isNaN(weekIndex)) {
            const dates = getWeekDates(selectedMonth, weekIndex);
            if (dates && dates[dayIndex]) {
              pdfDateSpan.textContent = String(dates[dayIndex].getDate()).padStart(2, '0');
            }
          }
        }
      }
    });
    tempContainer.appendChild(employeesTable);
    
    // نسخ جدول المشرفين
    const supervisorsTable = supervisorsContainer.cloneNode(true);
    // تعديل رؤوس جدول المشرفين لملف PDF
    supervisorsTable.querySelectorAll('th.vertical-header').forEach(th => {
      const dayNameSpan = th.querySelector('.day-name');
      const dateSpan = th.querySelector('.date');

      if (dayNameSpan && dateSpan) {
        const originalDateText = dateSpan.textContent;
        const originalDayNameText = dayNameSpan.textContent;
        th.innerHTML = `<div class="d-flex flex-column align-items-center"><span class="pdf-date">${originalDateText}</span><span class="pdf-day-name">${originalDayNameText}</span></div>`;
      }

      const pdfDateSpan = th.querySelector('.pdf-date');
      if (pdfDateSpan) {
        const parentThId = th.id;
        const parts = parentThId.split('-');
        if (parts.length >= 3) {
          const dayIndex = parseInt(parts[parts.length - 1]);
          let weekIndex = 0;
          if (parentThId.startsWith('supervisor-day')) {
            weekIndex = parseInt(parts[0].replace('supervisor-day', ''));
          }

          if (!isNaN(dayIndex) && !isNaN(weekIndex)) {
            const dates = getWeekDates(selectedMonth, weekIndex);
            if (dates && dates[dayIndex]) {
              pdfDateSpan.textContent = String(dates[dayIndex].getDate()).padStart(2, '0');
            }
          }
        }
      }
    });
    tempContainer.appendChild(supervisorsTable);
    
    // نسخ جدول توزيع الوقت إذا كان موجوداً
    const timeDistributionTable = document.querySelector('.table-responsive:has(#timeDistributionTable)');
    if (timeDistributionTable && selectedWeek !== 'all') {
      const timeTable = timeDistributionTable.cloneNode(true);
      // تعديل رؤوس جدول توزيع الوقت لملف PDF
      timeTable.querySelectorAll('th[id^="day"]').forEach(th => {
        const dayNameSpan = th.querySelector('.day-name');
        const dateSpan = th.querySelector('.date');

         if (dayNameSpan && dateSpan) {
          const originalDateText = dateSpan.textContent;
          const originalDayNameText = dayNameSpan.textContent;
          th.innerHTML = `<div class="d-flex flex-column align-items-center"><span class="pdf-date">${originalDateText}</span><span class="pdf-day-name">${originalDayNameText}</span></div>`;
        }

        const pdfDateSpan = th.querySelector('.pdf-date');
        if (pdfDateSpan) {
          const parentThId = th.id;
          const dayIndex = parseInt(parentThId.replace('day', '')) - 1;
          if (!isNaN(dayIndex)) {
            const dates = getWeekDates(selectedMonth, selectedWeek);
            if (dates && dates[dayIndex]) {
              pdfDateSpan.textContent = String(dates[dayIndex].getDate()).padStart(2, '0');
            }
          }
        }
      });
      tempContainer.appendChild(timeTable);
    }

    const opt = {
      margin: 1,
      filename: `جدول_الدوام_${monthName}_${weekText}.pdf`,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'cm', format: 'a4', orientation: 'landscape' }
    };

    // تحميل جميع الجداول كملف PDF واحد
    html2pdf().set(opt).from(tempContainer).save();
  };
};

function showDropdown(event, cell, type) {
  event.stopPropagation();
  currentCell = cell;
  currentCellType = type; // إضافة نوع الخلية (موظف أو مشرف)
  const dropdown = document.getElementById("dropdownMenu");
  dropdown.style.display = "block";
  dropdown.style.top = `${event.pageY}px`;
  dropdown.style.left = `${event.pageX - 120}px`;
}

function makeCellEditable(cell) {
  // إنشاء حقل إدخال
  const input = document.createElement('input');
  input.type = 'text';
  input.className = 'form-control form-control-sm text-center';
  input.style.width = '100%';
  input.style.height = '100%';
  input.style.padding = '0';
  input.style.margin = '0';
  input.style.border = '1px solid #dee2e6';
  input.style.borderRadius = '8px';
  input.style.fontSize = '14px';
  input.style.background = 'white';
  input.style.position = 'absolute';
  input.style.top = '0';
  input.style.left = '0';
  input.style.right = '0';
  input.style.bottom = '0';
  input.style.boxShadow = '0 0 0 2px rgba(0,123,255,0.25)';
  input.style.textAlign = 'center';
  input.dir = 'ltr';
  
  // جعل الخلية relative لتحديد موقع حقل الإدخال
  cell.style.position = 'relative';
  
  function validateAndUpdate() {
    const value = input.value.trim();
    if (value === '8' || value === '14') {
      const status = value === '8' ? '8 ساعات' : '14 ساعة';
      const cellType = value === '8' ? 'hours8' : 'hours14';
      updateCell(cell, status, cellType);
      input.remove();
    } else {
      // تغيير لون الإطار إلى أحمر عند القيمة غير الصحيحة
      input.style.border = '1px solid #dc3545';
      input.style.boxShadow = '0 0 0 2px rgba(220,53,69,0.25)';
      // مسح القيمة الموجودة
      input.value = '';
      // إعادة التركيز على الحقل
      input.focus();
    }
  }
  
  // إضافة مستمعي الأحداث
  input.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      validateAndUpdate();
    }
  });
  
  input.addEventListener('blur', function() {
    validateAndUpdate();
  });
  
  // إعادة تعيين لون الإطار عند الكتابة
  input.addEventListener('input', function() {
    input.style.border = '1px solid #dee2e6';
    input.style.boxShadow = '0 0 0 2px rgba(0,123,255,0.25)';
  });
  
  // تفريغ الخلية وإضافة حقل الإدخال
  cell.textContent = '';
  cell.appendChild(input);
  input.focus();
}

function updateCell(cell, status, cellType) {
  const type = cell.dataset.supervisor ? 'supervisor' : 'employee';
  const name = cell.dataset.supervisor || cell.dataset.employee;
  const id = cell.dataset.supervisorid || cell.dataset.employeeid;
  const date = new Date(cell.dataset.date);
  const dateStr = date.toISOString().split('T')[0];
  const key = `${id}_${dateStr}`;

  // تحديث البيانات
  if (type === 'supervisor') {
    supervisorsData[key] = { status, type: cellType };
    saveSupervisorsData();
  } else {
    scheduleData[key] = { status, type: cellType };
    saveScheduleData();
  }

  // إعادة عرض الجداول مباشرة
  renderTable();
}

function selectOption(type) {
  if (!currentCell) return;
  let status = "";
  let cellType = "";

  switch (type) {
    case "add":
      document.getElementById("dropdownMenu").style.display = "none";
      makeCellEditable(currentCell);
      return;
    case "leave":
      status = "إجازة";
      cellType = "leave";
      break;
    case "event":
      status = "مناسبة";
      cellType = "event";
      break;
    case "off":
      status = "OFF";
      cellType = "off";
      break;
    case "store":
      status = "ستور";
      cellType = "store";
      break;
  }

  updateCell(currentCell, status, cellType);

  document.getElementById("dropdownMenu").style.display = "none";
  currentCell = null;
}

function addEmployee() {
  // إظهار الـ modal
  const modal = new bootstrap.Modal(document.getElementById('addEmployeeModal'));
  modal.show();
  
  // تفريغ حقل الإدخال عند فتح الـ modal
  document.getElementById('employeeName').value = '';
  
  // إضافة مستمع حدث للـ Enter
  document.getElementById('employeeName').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      saveNewEmployee();
    }
  });
}

function saveNewEmployee() {
  const nameInput = document.getElementById('employeeName');
  const name = nameInput.value.trim();
  
  if (name) {
    // إضافة الموظف وحفظ البيانات
    employees.push(name);
    localStorage.setItem('employees', JSON.stringify(employees));
    
    // إغلاق المودال وتنظيف جميع التأثيرات
    const modalElement = document.getElementById('addEmployeeModal');
    modalElement.style.display = 'none';
    modalElement.classList.remove('show');
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
      backdrop.remove();
    }
    
    // تحديث الجدول وتفريغ حقل الإدخال
    renderTable();
    nameInput.value = '';
  }
}

// إضافة event listeners عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', () => {
  // إنشاء زر التحميل
  downloadBtn = document.createElement('button');
  downloadBtn.className = 'btn btn-primary ms-2';
  downloadBtn.innerHTML = '<i class="fas fa-download"></i> تحميل PDF';
  
  // إضافة الزر بجانب زر إضافة موظف
  const addEmployeeBtn = document.querySelector('.btn-primary');
  addEmployeeBtn.parentNode.insertBefore(downloadBtn, addEmployeeBtn.nextSibling);

  document.addEventListener("click", () => {
    document.getElementById("dropdownMenu").style.display = "none";
    currentCell = null;
  });

  // إزالة الكلاس active من جميع الأزرار أولاً
  document.querySelectorAll(".week-btn").forEach(btn => {
    btn.classList.remove("active");
  });

  // تعيين القيم المحفوظة في القائمة المنسدلة والأزرار
  document.getElementById("monthSelect").value = selectedMonth;
  document.getElementById("yearSelect").value = selectedYear;
  
  // تفعيل الزر المحدد فقط
  const activeButton = document.querySelector(`.week-btn[data-week="${selectedWeek}"]`);
  if (activeButton) {
    activeButton.classList.add("active");
  }

  document.getElementById("monthSelect").addEventListener("change", (e) => {
    selectedMonth = parseInt(e.target.value);
    saveSelectedMonthAndWeek();
    // التحقق من وجود الأسبوع الخامس عند تغيير الشهر
    const hasFifthWeek = checkFifthWeekExists(selectedMonth);
    const fifthWeekBtn = document.querySelector('.week-btn[data-week="4"]');
    if (fifthWeekBtn) {
      if (!hasFifthWeek) {
        fifthWeekBtn.style.display = 'none';
        // إذا كان الأسبوع المحدد هو الخامس، نعود للأسبوع الأول
        if (selectedWeek === '4') {
          selectedWeek = '0';
          // إزالة الكلاس active من جميع الأزرار
          document.querySelectorAll(".week-btn").forEach(btn => btn.classList.remove("active"));
          // تفعيل زر الأسبوع الأول
          document.querySelector('.week-btn[data-week="0"]').classList.add("active");
        }
      } else {
        fifthWeekBtn.style.display = 'inline-block';
      }
    }
    renderTable();
  });

  document.getElementById("yearSelect").addEventListener("change", (e) => {
    selectedYear = parseInt(e.target.value);
    saveSelectedMonthAndWeek();
    renderTable();
  });

  document.querySelectorAll(".week-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      // إزالة الكلاس active من جميع الأزرار
      document.querySelectorAll(".week-btn").forEach(b => b.classList.remove("active"));
      // تفعيل الزر المحدد فقط
      btn.classList.add("active");
      selectedWeek = btn.dataset.week;
      saveSelectedMonthAndWeek();
      renderTable();
    });
  });

  // إضافة السنوات بشكل ديناميكي
  const yearSelect = document.getElementById('yearSelect');
  const currentYear = new Date().getFullYear();
  yearSelect.innerHTML = ''; // مسح الخيارات الموجودة
  
  for (let year = currentYear; year <= 2030; year++) {
    const option = document.createElement('option');
    option.value = year;
    option.textContent = year;
    yearSelect.appendChild(option);
  }

  // تحميل الجدول مع القيم المحفوظة
  setTimeout(() => {
    renderTable();
  }, 500);

});

// دالة لحفظ الشهر والأسبوع المحدد
function saveSelectedMonthAndWeek() {
  localStorage.setItem('selectedMonth', selectedMonth);
  localStorage.setItem('selectedYear', selectedYear);
  localStorage.setItem('selectedWeek', selectedWeek);
}

// دالة لتحديث جدول توزيع الوقت
function updateTimeDistributionTable() {
  const dates = getWeekDates(selectedMonth, selectedWeek);
  
  // التحقق من وجود جدول توزيع الوقت
  const timeDistributionTable = document.getElementById('timeDistributionTable');
  if (!timeDistributionTable) return;
  
  // تحديث عناوين الأيام في الجدول
  dates.forEach((date, dayIndex) => {
    const dayHeader = document.getElementById(`day${dayIndex + 1}`);
    if (dayHeader) {
      dayHeader.innerHTML = `
        <span class="day-name">${days[date.getDay()]}</span>
        <span class="date">${formatDate(date)}</span>
      `;
    }
  });

  // إعادة تعيين العدادات
  const time8Cells = document.querySelectorAll('.time-8');
  const time14Cells = document.querySelectorAll('.time-14');
  const leaveCells = document.querySelectorAll('.leave');
  const offCells = document.querySelectorAll('.off');
  const storeCells = document.querySelectorAll('.store');

  // التحقق من وجود الخلايا قبل تحديثها
  if (time8Cells.length === 0 || time14Cells.length === 0 || 
      leaveCells.length === 0 || offCells.length === 0 || 
      storeCells.length === 0) return;

  // إعادة تعيين الألوان والعدادات
  time8Cells.forEach(cell => {
    if (cell) {
      cell.textContent = '0';
      cell.className = 'text-center time-8 bg-hours8';
    }
  });
  time14Cells.forEach(cell => {
    if (cell) {
      cell.textContent = '0';
      cell.className = 'text-center time-14 bg-hours14';
    }
  });
  leaveCells.forEach(cell => {
    if (cell) {
      cell.textContent = '0';
      cell.className = 'text-center leave bg-leave';
    }
  });
  offCells.forEach(cell => {
    if (cell) {
      cell.textContent = '0';
      cell.className = 'text-center off bg-off';
    }
  });
  storeCells.forEach(cell => {
    if (cell) {
      cell.textContent = '0';
      cell.className = 'text-center store bg-store';
    }
  });

  // حساب التوزيع لكل يوم
  dates.forEach((date, dayIndex) => {
    const dateStr = date.toISOString().split('T')[0];
    let time8Count = 0;
    let time14Count = 0;
    let leaveCount = 0;
    let offCount = 0;
    let storeCount = 0;

    employees.forEach(emp => {
      const key = `${emp.id}_${dateStr}`;
      const cellData = scheduleData[key] || {};
      
      switch(cellData.type) {
        case 'hours8':
          time8Count++;
          break;
        case 'hours14':
          time14Count++;
          break;
        case 'leave':
          leaveCount++;
          break;
        case 'off':
          offCount++;
          break;
        case 'store':
          storeCount++;
          break;
      }
    });

    // تحديث الخلايا في جدول التوزيع مع التحقق من وجودها
    if (time8Cells[dayIndex]) time8Cells[dayIndex].textContent = time8Count;
    if (time14Cells[dayIndex]) time14Cells[dayIndex].textContent = time14Count;
    if (leaveCells[dayIndex]) leaveCells[dayIndex].textContent = leaveCount;
    if (offCells[dayIndex]) offCells[dayIndex].textContent = offCount;
    if (storeCells[dayIndex]) storeCells[dayIndex].textContent = storeCount;
  });
} 