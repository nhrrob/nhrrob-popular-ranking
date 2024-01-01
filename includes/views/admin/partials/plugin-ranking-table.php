<div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden mt-5">
    <div class="flex flex-col px-4 py-3 space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:space-x-4">
        <div class="flex items-center space-x-4">
            <h5>
                <?php
                $page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
                $page = $page >= 1 && $page <= 20 ? $page : 1;
                ?>
                <?php
                $transient_name = "{$this->transient_name}_{$page}";
                $updated_at = get_option($transient_name . '_fetched_at');
                $updated_at = human_time_diff($updated_at, current_time('U'));
                ?>
                <span class="text-gray-500"><?php echo esc_html__('Updated:', 'nhrrob-popular-plugins'); ?></span>
                <span class="dark:text-white"><?php printf('%s %s', esc_html($updated_at), __('ago.', 'nhrrob-popular-plugins')); ?></span>
            </h5>
        </div>
        <div class="flex items-center flex-1 space-x-4">
            <input class="m-auto w-3/6 npp-username-input" type="text" name="username" placeholder="nhrrob" value="<?php echo ! empty( $_GET['username'] ) ? sanitize_text_field( $_GET['username'] ) : ''; ?>">
        </div>
        
        <div class="flex flex-col flex-shrink-0 space-y-3 md:flex-row md:items-center lg:justify-end md:space-y-0 md:space-x-3">
            <a type="button" href="<?php echo esc_url( admin_url( "admin.php?page={$this->page_slug}&paged={$page}&username={$this->username}&cache_clear" ) ); ?>" class="flex items-center justify-center flex-shrink-0 px-3 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg focus:outline-none hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" fill="none" viewbox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <?php esc_html_e('Cache Clear', 'nhrrob-popular-plugins'); ?>
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-4 py-3">Rank</th>
                    <th scope="col" class="px-4 py-3">Active Installs</th>
                    <th scope="col" class="px-4 py-3">Name</th>
                    <th scope="col" class="px-4 py-3">Author</th>
                    <th scope="col" class="px-4 py-3">5 Stars</th>
                    <th scope="col" class="px-4 py-3">1 Stars</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $success_class  = 'text-green-500';
                $danger_class   = 'text-red-500';
                $primary_class  = 'text-blue-500';
                $info_class     = 'text-indigo-500';

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
                    <tr class="border-b dark:border-gray-700">
                        <th scope="row" class="<?php echo esc_attr( $rank_diff_class ); ?> px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"><?php printf('(%s) %s', esc_html($rank_diff), esc_html(intval($plugin['rank']))); ?></th>
                        <td class="<?php echo esc_attr( $active_installs_diff_class ); ?> px-4 py-3"><?php printf('(%s) %s+', esc_html($active_installs_diff), esc_html($this->shortNumber(intval($plugin['active_installs'])))); ?></td>
                        <td class="px-4 py-3"><?php printf('<a target="_blank" href="%s">%s</a> ', sanitize_url("https://wordpress.org/plugins/{$plugin['slug']}"), wp_trim_words(sanitize_text_field($plugin['plugin']->name), 4)); ?></td>
                        <td class="px-4 py-3"><?php echo wp_kses_post($plugin['plugin']->author); ?></td>
                        <td class="<?php echo esc_attr( $stars5_diff_class ); ?> px-4 py-3"><?php printf('(%s) %s', esc_html($stars5_diff), esc_html(intval($plugin['plugin']->ratings['5']))); ?></td>
                        <td class="<?php echo esc_attr( $stars1_diff_class ); ?> px-4 py-3"><?php printf('(%s) %s', esc_html($stars1_diff), esc_html(intval($plugin['plugin']->ratings['1']))); ?></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4" aria-label="Table navigation">
        <!-- <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
            Showing
            <span class="font-semibold text-gray-900 dark:text-white">1-10</span>
            of
            <span class="font-semibold text-gray-900 dark:text-white">1000</span>
        </span> -->
        <ul class="inline-flex items-stretch -space-x-px">
            <?php $prev_page = $page - 1 > 0 ? $page - 1 : 1; ?>
            <?php $next_page = $page + 1; ?>
            <li>
                <a href="<?php echo esc_url(admin_url("admin.php?page={$this->page_slug}&paged={$prev_page}&username={$this->username}")); ?>" class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="sr-only">Previous</span>
                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url(admin_url("admin.php?page={$this->page_slug}&paged=1&username={$this->username}")); ?>" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">1</a>
            </li>
            <li>
                <a href="<?php echo esc_url(admin_url("admin.php?page={$this->page_slug}&paged=2&username={$this->username}")); ?>" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">2</a>
            </li>
            <li>
                <a href="<?php echo esc_url(admin_url("admin.php?page={$this->page_slug}&paged=3&username={$this->username}")); ?>" aria-current="page" class="flex items-center justify-center text-sm z-10 py-2 px-3 leading-tight text-primary-600 bg-primary-50 border border-primary-300 hover:bg-primary-100 hover:text-primary-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">3</a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">...</a>
            </li>
            <li>
                <a href="<?php echo esc_url(admin_url("admin.php?page={$this->page_slug}&all&username={$this->username}")); ?>" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">All</a>
            </li>
            <li>
                <a href="<?php echo esc_url(admin_url("admin.php?page={$this->page_slug}&paged={$next_page}&username={$this->username}")); ?>" class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="sr-only">Next</span>
                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            </li>
        </ul>
    </nav>
</div>
<?php
