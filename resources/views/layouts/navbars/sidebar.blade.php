<div class="sidebar">
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="#" class="simple-text logo-mini">{{ _('WM') }}</a>
            <a href="#" class="simple-text logo-normal">{{ _('WAKE Marketing Pvt. Ltd') }}</a>
        </div>
        <ul class="nav">
            <li @if ($pageSlug == 'dashboard') class="active " @endif>
                <a href="{{ route('home') }}">
                    <i class="tim-icons icon-chart-pie-36"></i>
                    <p>{{ _('Dashboard') }}</p>
                </a>
            </li>
            <li @if ($pageSlug == 'reports') class="active " @endif>
                <a href="{{ route('reports.index') }}">
                    <i class="tim-icons icon-paper"></i>
                    <p>{{ _('Reports') }}</p>
                </a>
            </li>
            <li @if ($pageSlug == 'profile') class="active " @endif>
                <a href="{{ route('profile.edit')  }}">
                    <i class="tim-icons icon-single-02"></i>
                    <p>{{ _('User Profile') }}</p>
                </a>
            </li>
        </ul>
    </div>
</div>
