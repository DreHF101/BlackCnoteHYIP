<div class="page-wrapper default-version">
    <?php
    hyiplab_include('admin/partials/sidenav');
    hyiplab_include('admin/partials/topnav');
    ?>
    <div class="body-wrapper">
        <div class="bodywrapper__inner">
            <?php
            hyiplab_include('admin/partials/breadcrumb', compact('pageTitle'));
            ?>
            {{yield}}
        </div>
    </div>
</div>
<?php
hyiplab_include('partials/notify');
?>