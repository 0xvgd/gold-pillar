import '@fullcalendar/core/main.css'
import '@fullcalendar/daygrid/main.css'
import '@fullcalendar/timegrid/main.css'
import '@fullcalendar/list/main.css'
import Vue from 'vue'
import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction';
import { ajaxSubmit } from '../../utils/forms'
import moment from '../../utils/moment'

const el = document.getElementById('calendar')

const app = new Vue({
  el: '#view-event-modal',
  data() {
    return {
      event: null,
      view: null
    }
  },
  methods: {
    viewEvent(id) {
      $.ajax({
        url: `${el.dataset.feedUrl}/${id}`,
        success: (response) => {
          this.event = response.event
          this.event.date = moment(this.event.date).utc(false).format('DD/MM/YYYY')
          this.event.start = moment(this.event.start).utc(false).format('HH:mm')
          this.event.end = moment(this.event.end).utc(false).format('HH:mm')
          this.view = response.view
          $('#view-event-modal').modal('show')
        }
      })
    },
    removeEvent() {
      const ok = confirm('Do you really want to remove this event?')
      if (ok) {
        $.ajax({
          url: `${el.dataset.feedUrl}/${this.event.id}`,
          method: 'DELETE',
          complete() {
            calendar.refetchEvents()
            $('#view-event-modal').modal('hide')
          }
        })
      }
      setTimeout(() => {
        $('[type=submit]').prop('disabled', false)
      }, 100)
      return false
    }
  }
})

const calendar = new Calendar(el, {
  timeZone: 'UTC',
  plugins: [ dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin ],
  events: el.dataset.feedUrl,
  firstDay: 1,
  header: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay'
  },
  dateClick: function(info) {
    $('#event_date').val(info.dateStr)
    $('#new-event-modal').modal('show')
  },
  eventClick: function(info) {
    app.viewEvent(info.event.id)
  }
})

calendar.render()

ajaxSubmit('#new-event-modal form', {
  success() {
    calendar.refetchEvents()
    $('#new-event-modal').modal('hide')
  }
})

$('#event_allDay').on('change', (e) => {
  if (e.target.checked) {
    $('#time-selection').hide();
  } else {
    $('#time-selection').show();
  }
})