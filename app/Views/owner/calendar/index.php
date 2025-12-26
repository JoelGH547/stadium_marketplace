<?= $this->extend('owner/layout/toptitle') ?>

<?= $this->section('content') ?>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

<div class="card shadow-lg border-0 rounded-4">
    <div class="card-header bg-white py-4">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="fas fa-calendar-alt me-2 text-primary"></i> ปฏิทินรายการจอง
        </h5>
    </div>
    <div class="card-body">
        <div id='calendar'></div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventTitle">รายละเอียดการจอง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventBody">
                <p><strong>สถานะ:</strong> <span id="eventStatus"></span></p>
                <p><strong>เวลา:</strong> <span id="eventTime"></span></p>
                <p><strong>ราคา:</strong> ฿<span id="eventPrice"></span></p>
                <div class="text-end">
                     <a href="#" id="viewFullDetails" class="btn btn-primary btn-sm">ดูรายละเอียดเต็ม</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'th', // Set locale to Thai
      navLinks: true, // Enable clickable date headings
      navLinkDayClick: 'timeGridDay', // Switch to day view on click
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      buttonText: {
        today: 'วันนี้',
        month: 'เดือน',
        week: 'สัปดาห์',
        day: 'วัน',
        list: 'รายการ'
      },
      events: '<?= base_url('owner/calendar/events') ?>',
      eventClick: function(info) {
        var event = info.event;
        var props = event.extendedProps;
        
        let statusText = props.status;
        if(props.status == 'pending') statusText = 'รอตรวจสอบ';
        if(props.status == 'approved' || props.status == 'paid' || props.status == 'confirmed') statusText = 'ยืนยันแล้ว';
        if(props.status == 'rejected') statusText = 'ปฏิเสธ';
        if(props.status == 'cancelled') statusText = 'ยกเลิก';

        document.getElementById('eventTitle').innerText = event.title;
        document.getElementById('eventStatus').innerText = statusText;
        document.getElementById('eventPrice').innerText = props.price;
        
        // Format Date Thai
        const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute:'2-digit' };
        document.getElementById('eventTime').innerText = event.start.toLocaleString('th-TH', options) + ' - ' + (event.end ? event.end.toLocaleString('th-TH', options) : '');
        
        document.getElementById('viewFullDetails').href = '<?= base_url('owner/bookings') ?>'; // Or detail link
        
        var modal = new bootstrap.Modal(document.getElementById('eventModal'));
        modal.show();
      }
    });
    calendar.render();
  });
</script>

<?= $this->endSection() ?>
