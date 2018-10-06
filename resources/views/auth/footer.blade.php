<script>
    var resizefunc = [];
</script>

<!-- Main  -->
{!! Html::script('assets/js/jquery.min.js') !!}
{!! Html::script('assets/js/bootstrap.min.js') !!}
{!! Html::script('assets/js/detect.js') !!}
{!! Html::script('assets/js/fastclick.js') !!}
{!! Html::script('assets/js/jquery.slimscroll.js') !!}
{!! Html::script('assets/js/jquery.blockUI.js') !!}
{!! Html::script('assets/js/waves.js') !!}
{!! Html::script('assets/js/wow.min.js') !!}
{!! Html::script('assets/js/jquery.nicescroll.js') !!}
{!! Html::script('assets/js/jquery.scrollTo.min.js') !!}

<!-- Custom main Js -->
{!! Html::script('assets/js/jquery.core.js') !!}
{!! Html::script('assets/js/jquery.app.js') !!}

<!-- Countdown -->
{!! Html::script('../plugins/countdown/dest/jquery.countdown.min.js') !!}
{!! Html::script('../plugins/simple-text-rotator/jquery.simple-text-rotator.min.js') !!}


<script type="text/javascript">
    /*$(document).ready(function () {

        // Countdown
        // To change date, simply edit: var endDate = "January 17, 2017 20:39:00";
        $(function () {
            var endDate = "January 17, 2018 20:39:00";
            $('.app-countdown .row').countdown({
                date: endDate,
                render: function (data) {
                    $(this.el).html('<div><div><span class="text-primary">' + (parseInt(this.leadingZeros(data.years, 2) * 365) + parseInt(this.leadingZeros(data.days, 2))) + '</span><span><b>Days</b></span></div><div><span class="text-primary">' + this.leadingZeros(data.hours, 2) + '</span><span><b>Hours</b></span></div></div><div class=""><div><span class="text-primary">' + this.leadingZeros(data.min, 2) + '</span><span><b>Minutes</b></span></div><div><span class="text-primary">' + this.leadingZeros(data.sec, 2) + '</span><span><b>Seconds</b></span></div></div>');
                }
            });
        });

        // Text rotate
        $(".home-text .rotate").textrotator({
            animation: "fade",
            speed: 3000
        });
    });*/

</script>


</body>
</html>