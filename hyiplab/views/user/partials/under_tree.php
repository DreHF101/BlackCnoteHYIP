<ul <?php if ($isFirst) { ?>class="firstList" <?php } ?>>
    <?php foreach (getViserAllReferrer($user->ID) as $key => $under) {
        $under = get_userdata($under->id);
    ?>
        <?php if ($key == 0) { ?>
            <?php $layer++; ?>
        <?php } ?>
        <li><?php echo esc_html($under->display_name); ?> ( <?php echo esc_html($under->user_login); ?> )
            <?php if (count(getViserAllReferrer($under->ID)) > 0 && ($layer < $maxLevel)) { ?>
                <?php hyiplab_include('user/partials/under_tree', ['user' => $under, 'layer' => $layer, 'isFirst' => false, 'maxLevel' => $maxLevel]); ?>
            <?php } ?>
        </li>
    <?php } ?>
</ul>