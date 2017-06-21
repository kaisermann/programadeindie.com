<table class="icit-profiler-table icit-profiler-table-zebra icit-profiler-table-plugins">
    <tr>
        <th>Plugin</th>
        <th>Average</th>
        <th>Front-end</th>
        <th>Admin</th>
        <th>AJAX</th>
        <th>Cron</th>
    </tr>

    <?php foreach( $data as $plugin ):?>
        <tr>
            <td><?php echo $plugin['plugin']?></td>
            <td class="numeric-column"><?php echo isset( $plugin['average'] ) ? number_format( $plugin['average'], 2 ) : '-'?></td>
            <td class="numeric-column"><?php echo isset( $plugin['front'] ) ? number_format( $plugin['front'], 2 ) : '-'?></td>
            <td class="numeric-column"><?php echo isset( $plugin['admin'] ) ? number_format( $plugin['admin'], 2 ) : '-'?></td>
            <td class="numeric-column"><?php echo isset( $plugin['ajax'] ) ? number_format( $plugin['ajax'], 2 ) : '-'?></td>
            <td class="numeric-column"><?php echo isset( $plugin['cron'] ) ? number_format( $plugin['cron'], 2 ) : '-'?></td>
        </tr>
    <?php endforeach?>
</table>
