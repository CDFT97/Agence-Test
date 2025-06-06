// Demo 4 Js file
$(document).ready(function () {
    'use strict';

	if ( $.fn.countdown ) {
    	// Deal of the day countdown
		$('.daily-deal-countdown').each(function () {
			var $this = $(this),
				untilDate = $this.data('until'),
				compact = $this.data('compact');

			$this.countdown({
			    until: untilDate, // this is relative date +10h +5m vs..
			    format: 'HMS',
			    padZeroes: true,
			    labels: ['years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds'],
			    labels1: ['year', 'month', 'week', 'day', 'hour', 'minutes', 'second']
			});
		});

		// Pause
		// $('.daily-deal-countdown').countdown('pause');


		// Offer countdown
		$('.offer-countdown').each(function () {
			var $this = $(this),
				untilDate =new Date( $this.data('until')),
				compact = $this.data('compact');

			$this.countdown({
			    until: untilDate, // this is relative date +10h +5m vs..
			    labels: ['Años', 'Meses', 'Semanas', 'Días', 'Horas', 'Minutos', 'Segundos'],
			    labels1: ['Año', 'Mes', 'Semana', 'Día', 'Hora', 'Minuto', 'Segundo']
			});
		});

		// Pause
		// $('.offer-countdown').countdown('pause');
	}
});
