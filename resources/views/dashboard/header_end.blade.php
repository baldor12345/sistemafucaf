    {!! Html::style('assets/css/bootstrap.min.css') !!}
    {!! Html::style('assets/css/core.css') !!}
    {!! Html::style('assets/css/icons.css') !!}
    {!! Html::style('assets/css/components.css') !!}
    {!! Html::style('assets/css/pages.css') !!}
    {!! Html::style('assets/css/menu.css') !!}
    {!! Html::style('assets/css/responsive.css') !!}

    {!! Html::style('plugins/timepicker/bootstrap-timepicker.min.css') !!}
    {!! Html::style('plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') !!}
    {!! Html::style('plugins/bootstrap-daterangepicker/daterangepicker.css') !!}

    {{-- typeahead.js-bootstrap: para autocompletar --}}
    {!! HTML::style('plugins/x-editable/dist/inputs-ext/typeaheadjs/lib/typeahead.js-bootstrap.css', array('media' => 'screen')) !!}

    {!! Html::script('assets/js/modernizr.min.js') !!}

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    
</head>


<body class="fixed-left">


@include('dashboard.topbar')
