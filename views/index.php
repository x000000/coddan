<label>
	<input id="client-sort" type="checkbox"/> Client sort
</label>

<table id="content-table" class="table table-striped">
	<thead>
		<tr>
			<?php
				foreach ($columns as $col => $abc) :
					$active = $activeSort[0] === $col;
					$sort   = $active && $activeSort[1] === 'asc'
						? 'desc'
						: 'asc';
			?>
				<th>
					<a href="?sort=<?= urlencode("$col.$sort"); ?>"
					   class="sort <?= $active ? $activeSort[1] : ''; ?> <?= $abc ? 'abc' : ''; ?>">
						<?= htmlspecialchars($col); ?>
					</a>
				</th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($dataProvider as $row) : ?>
			<tr>
				<?php foreach ($columns as $col => $abc) : ?>
					<td><?= htmlspecialchars($row[$col]); ?></td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<!--
-->
