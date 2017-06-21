<?php if( empty( $data ) ):?>
    <p>To view in-depth information on a specific request, select it from the <a href="admin.php?page=icit-profiler&tab=requests">requests</a> tab.</p>
    <p>Not all requests have detailed information available due to the level of logging.</p>
<?php else:?>
    <table class="icit-profiler-table icit-profiler-table-summary">
        <tr>
            <th>URL</th>
            <td><?php echo $request->request?></td>
        </tr>
        <tr>
            <th>Date</th>
            <td><?php echo date( 'd-m-Y H:i:s', $request->timestamp )?></td>
        </tr>
        <tr>
            <th>Type</th>
            <td><?php echo $request->type?></td>
        </tr>
        <?php if( ! empty( $request->template) ):?>
            <tr>
                <th>Template</th>
                <td><?php echo $request->template?></td>
            </tr>
        <?php endif?>
        <tr>
            <th>Memory</th>
            <td><?php echo $request->memory?></td>
        </tr>
        <tr>
            <th>Database Queries</th>
            <td><?php echo $request->queries?> (<a href="<?php echo admin_url( 'admin.php?page=icit-profiler&tab=database&request_id=' . $request->id )?>">View</a>)</td>
        </tr>
    </table>

    <table class="icit-profiler-table icit-profiler-table-functions">
        <tr>
            <th colspan="2">Plugin</th>
            <th>Duration</th>
            <th>Count</th>
        </tr>
        <?php foreach( $data as $plugin ):?>
            <tr class="summary" data-plugin="<?php echo $plugin['plugin']?>">
                <th colspan="2"><?php echo $plugin['plugin']?></th>
                <th><?php echo $plugin['duration']?></th>
                <th><?php echo $plugin['count']?></th>
            </tr>

            <?php foreach( $plugin['functions'] as $function ):?>
                <tr class="detail plugin-<?php echo $plugin['plugin']?>">
                    <td></td>
                    <td><?php echo $function->function?></td>
                    <td><?php echo $function->duration?></td>
                    <td><?php echo $function->count?></td>
                </tr>
            <?php endforeach?>
        <?php endforeach?>
    </table>

    <?php if( ! empty( $payload ) ):?>
        <h3>Payload</h3>
        <pre><?php print_r( $payload )?></pre>
    <?php endif?>
<?php endif?>
