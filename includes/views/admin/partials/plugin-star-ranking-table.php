<table class="table table-bordered table-responsive" id="nhrrob-popular-plugins-by-stars">
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
        $excluded_plugins = $this->get_excluded_plugins_list();

        $counter = 0;

        foreach ($this->popular_plugins_stars as $index => $plugin_stars) {
            $counter++;

            $author_profile = str_replace('https://profiles.wordpress.org/', '', $plugin_stars['author_profile']);
            $author_profile = '@' . rtrim($author_profile, '/');

            if (!$all) {
                if ($plugin_stars['author_profile'] !== "https://profiles.wordpress.org/{$this->username}/") {
                    continue;
                }

                if (in_array($plugin_stars['slug'], $excluded_plugins)) {
                    continue;
                }

                $author_profile = $plugin_stars['plugin']->author;
            }

            // $rank_diff          = intval( $plugin['rank_old'] ) - intval( $plugin['rank'] );
            // $rank_diff_class    = $rank_diff > 0 ? $success_class : '';
            // $rank_diff_class    = $rank_diff > 20 ? $primary_class : $rank_diff_class;
            // $rank_diff_class    = $rank_diff < -20 ? $danger_class : $rank_diff_class;

            // $active_installs_diff_raw   = intval($plugin['active_installs']) - intval($plugin['active_installs_old']);
            // $active_installs_diff       = number_format( $active_installs_diff_raw );
            // $active_installs_diff_class = $active_installs_diff_raw > 0 ? $success_class : '';

            // $stars5_diff        = intval( $plugin['rating5'] ) - intval( $plugin['rating5_old'] );
            // $stars5_diff_class  = $stars5_diff > 0 ? $success_class : '';

            // $stars1_diff        = intval( $plugin['rating1'] ) - intval( $plugin['rating1_old'] );
            // $stars1_diff_class  = $stars1_diff > 0 ? $danger_class : '';
            // echo "<pre>";
            // print_r($plugin_stars);
        ?>
            <tr>
                <th style="font-weight: normal;"><?php printf('%d', intval($counter)); ?></th>
                <td><?php printf('%s', esc_html(number_format(!empty($plugin_stars['active_installs']) ? intval($plugin_stars['active_installs']) : 0))); ?></td>
                <td width="30%"><?php printf('<a href="%s">%s</a> ', sanitize_url("https://wordpress.org/plugins/{$plugin_stars['slug']}"), wp_trim_words(!empty($plugin_stars['name']) ? sanitize_text_field($plugin_stars['name']) : '', 4)); ?></td>
                <td width="20%"><?php echo wp_kses_post($author_profile); ?></td>
                <td><?php printf('%s', esc_html(intval($plugin_stars['rating5']))); ?></td>
                <td><?php printf('%s', esc_html(intval($plugin_stars['rating1']))); ?></td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>