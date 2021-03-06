<div class="alertbanner alertbanner--$Modifier" role='alert' aria-hidden='true' style="display:none;">
    <div class="alertbanner__inner">
        <div class="alertbanner__icon">
            &nbsp;
        </div>
        <div class="alertbanner__body">
            <h2 class="alertbanner__title">
                $Title
            </h2>
            <div class="alertbanner__content">
                <% if $Content.RichLinks %>
                    $Content.RichLinks
                <% else %>
                    $Content
                <% end_if %>
            </div>
        </div>
        <button class="alertbanner__close" data-banner-id="$ID">
            <span class="sr-only">Close alert</span>
        </button>
    </div>
</div>