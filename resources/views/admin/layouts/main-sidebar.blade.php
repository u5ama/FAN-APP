<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="{{ route('admin.dashboard') }}">
                        <img src="{{URL::asset('assets/img/brand/logo_black.png')}}" class="main-logo" alt="logo">
        </a>
        <a class="desktop-logo logo-dark active" href="{{ route('admin.dashboard') }}">
                        <img src="{{URL::asset('assets/img/brand/logo.png')}}" class="main-logo dark-theme" alt="logo">
        </a>
        <a class="logo-icon mobile-logo icon-light active" href="{{ route('admin.dashboard') }}">
                        <img src="{{URL::asset('assets/img/brand/logo_black.png')}}" class="logo-icon" alt="logo">
        </a>
        <a class="logo-icon mobile-logo icon-dark active" href="{{ route('admin.dashboard') }}">
                        <img src="{{URL::asset('assets/img/brand/logo.png')}}" class="logo-icon dark-theme" alt="logo">
        </a>
    </div>

    <div class="main-sidemenu">
        <ul class="side-menu mt-3">
            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin.dashboard') }}">
                    <i class="fa fa-tachometer-alt side-menu__icon"></i>
                    <span class="side-menu__label">{{ config('languageString.dashboard') }}</span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin.players.index') }}">
                    <i class="fa fa-user side-menu__icon"></i>
                    <span class="side-menu__label">Players</span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin.user.index') }}">
                    <i class="fa fa-user side-menu__icon"></i>
                    <span class="side-menu__label">Fans</span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin.schools.index') }}">
                    <i class="fa fa-list-alt side-menu__icon"></i>
                    <span class="side-menu__label">Schools</span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin.teams.index') }}">
                    <i class="fa fa-list-alt side-menu__icon"></i>
                    <span class="side-menu__label">Teams</span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin.gifts.index') }}">
                    <i class="fa fa-gift side-menu__icon"></i>
                    <span class="side-menu__label">Gifts</span>
                </a>
            </li>
            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin.gifts_sent.index') }}">
                    <i class="fa fa-bus-alt side-menu__icon"></i>
                    <span class="side-menu__label">Gifts Sent</span>
                </a>
            </li>
            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin.payments.index') }}">
                    <i class="fa fa-dollar-sign side-menu__icon"></i>
                    <span class="side-menu__label">Invoices</span>
                </a>
            </li>
            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin.recommendations.index') }}">
                    <i class="fa fa-envelope side-menu__icon"></i>
                    <span class="side-menu__label">Recommendations</span>
                </a>
            </li>

{{--            <li class="slide">--}}
{{--                <a class="side-menu__item" href="{{ route('admin.notifications.index') }}">--}}
{{--                    <i class="fa fa-bell side-menu__icon"></i>--}}
{{--                    <span class="side-menu__label">Notifications</span>--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            <li class="slide">--}}
{{--                <a class="side-menu__item" href="{{ route('admin.wallets.index') }}">--}}
{{--                    <i class="fa fa-wallet side-menu__icon"></i>--}}
{{--                    <span class="side-menu__label">Wallets</span>--}}
{{--                </a>--}}
{{--            </li>--}}


            {{--<li class="slide">
                <a class="side-menu__item" href="{{ route('admin.contact-us') }}">
                    <i class="fas fa-address-book side-menu__icon"></i>
                    <span class="side-menu__label">{{ config('languageString.contact_us') }}</span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin.report-problem') }}">
                    <i class="fa fa-file side-menu__icon"></i>
                    <span class="side-menu__label">{{ config('languageString.report_problem') }}</span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin.notification.index') }}">
                    <i class="fa fa-bell side-menu__icon"></i>
                    <span class="side-menu__label">{{ config('languageString.send_notification') }}</span>
                </a>
            </li>--}}



{{--            <li class="side-item side-item-category">{{config('languageString.app_setting')}}</li>--}}

{{--            <li class="slide">--}}
{{--                <a class="side-menu__item" href="{{ route('admin.page.index') }}">--}}
{{--                    <i class="fa fa-file side-menu__icon"></i>--}}
{{--                    <span class="side-menu__label">{{config('languageString.text_page')}}</span>--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            <li class="slide">--}}
{{--                <a class="side-menu__item" href="{{ route('admin.social-link.index') }}">--}}
{{--                    <i class="fa fa-globe-europe side-menu__icon"></i>--}}
{{--                    <span class="side-menu__label">{{config('languageString.social_link')}}</span>--}}
{{--                </a>--}}
{{--            </li>--}}

            {{--<li class="slide">
                <a class="side-menu__item" href="{{ route('admin.app-control.index') }}">
                    <i class="fa fa-file-word side-menu__icon"></i>
                    <span class="side-menu__label">{{config('languageString.app_control')}}</span>
                </a>
            </li>--}}

{{--            <li class="slide">--}}
{{--                <a class="side-menu__item" href="{{ route('admin.app-menu.index') }}">--}}
{{--                    <i class="fa fa-bars side-menu__icon"></i>--}}
{{--                    <span class="side-menu__label">App Menu</span>--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            <li class="slide">--}}
{{--                <a class="side-menu__item" href="{{ route('admin.setting') }}">--}}
{{--                    <i class="fa fa-cog side-menu__icon"></i>--}}
{{--                    <span class="side-menu__label">{{config('languageString.setting')}}</span>--}}
{{--                </a>--}}
{{--            </li>--}}
        </ul>
    </div>
</aside>
<!-- main-sidebar -->
