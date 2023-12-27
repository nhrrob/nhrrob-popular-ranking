<table class="table table-bordered table-responsive" id="nhrrob-popular-plugins">
    <thead>
        <tr>
            <th scope="col">Rank</th>
            <th scope="col">Active Installs</th>
            <th scope="col">Name</th>
            <th scope="col">Author</th>
            <th scope="col">5 Stars</th>
            <th scope="col">1 Stars</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $success_class = 'bg-success text-white';
        $danger_class = 'bg-danger text-white';
        $primary_class = 'bg-primary text-white';
        $info_class = 'bg-info';

        foreach ($popular_plugins as $index => $plugin) {

            // printf('<p>(%d) %d : %s (%s) - <a href="%s">%s</a> (%s - %s) - 5 stars: %d, 1 stars: %d</p>', intval($plugin['rank_old']) - intval($plugin['rank']), intval($plugin['rank']), number_format( intval( $plugin['active_installs'] ) ), number_format( intval($plugin['active_installs']) - intval($plugin['active_installs_old']) ), sanitize_url( "https://wordpress.org/plugins/{$plugin['slug']}" ), sanitize_text_field( $plugin['plugin']->name ), sanitize_text_field( $plugin['slug'] ), wp_kses_post( $plugin['plugin']->author ), intval( $plugin['plugin']->ratings['5'] ), intval( $plugin['plugin']->ratings['1'] ) );
            $rank_diff          = intval($plugin['rank_old']) - intval($plugin['rank']);
            $rank_diff_class    = $rank_diff > 0 ? $success_class : '';
            $rank_diff_class    = $rank_diff > 20 ? $primary_class : $rank_diff_class;
            $rank_diff_class    = $rank_diff < -20 ? $danger_class : $rank_diff_class;

            $active_installs_diff_raw   = intval($plugin['active_installs']) - intval($plugin['active_installs_old']);
            $active_installs_diff       = number_format($active_installs_diff_raw);
            $active_installs_diff_class = $active_installs_diff_raw > 0 ? $success_class : '';

            $stars5_diff        = intval($plugin['rating5']) - intval($plugin['rating5_old']);
            $stars5_diff_class  = $stars5_diff > 0 ? $success_class : '';

            $stars1_diff        = intval($plugin['rating1']) - intval($plugin['rating1_old']);
            $stars1_diff_class  = $stars1_diff > 0 ? $danger_class : '';
        ?>
            <tr>
                <th class="<?php echo esc_attr($rank_diff_class); ?>" style="font-weight: normal;"><?php printf('(%s) %s', esc_html($rank_diff), esc_html(intval($plugin['rank']))); ?></th>
                <td class="<?php echo esc_attr($active_installs_diff_class); ?>"><?php printf('(%s) %s', esc_html($active_installs_diff), esc_html(number_format(intval($plugin['active_installs'])))); ?></td>
                <td width="30%"><?php printf('<a href="%s">%s</a> ', sanitize_url("https://wordpress.org/plugins/{$plugin['slug']}"), wp_trim_words(sanitize_text_field($plugin['plugin']->name), 4)); ?></td>
                <td width="20%"><?php echo wp_kses_post($plugin['plugin']->author); ?></td>
                <td class="<?php echo esc_attr($stars5_diff_class); ?>"><?php printf('(%s) %s', esc_html($stars5_diff), esc_html(intval($plugin['plugin']->ratings['5']))); ?></td>
                <td class="<?php echo esc_attr($stars1_diff_class); ?>"><?php printf('(%s) %s', esc_html($stars1_diff), esc_html(intval($plugin['plugin']->ratings['1']))); ?></td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<?php 