<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<title>Coddan Design test case</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />

	<style type="text/css">
		.navbar-brand {
			height: auto;
			padding-top: 4px;
			padding-bottom: 3px;
			margin: 5px 0 0;
		}
		.navbar-brand img {
			height: 32px;
		}

		.sort.asc:before {
			content: '↑';
			display: inline;
		}
		.sort.desc:before {
			content: '↓';
			display: inline;
		}
	</style>
</head>
<body>

<div class="wrap">
	<header class="navbar navbar-default navbar-static-top">
		<div class="container">
			<div class="navbar-header">
				<a href="/" class="navbar-brand">
					<img src="http://coddan-design.com/assets/images/logo.svg" alt="" />
				</a>
			</div>
		</div>
	</header>

	<div class="container">
		<?= $content ?>
	</div>
</div>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script type="text/javascript">
	$(function() {
		var table = '#content-table';

		$(document)
			.on('click', table + ' .sort', function() {
				if ($('#client-sort').is(':checked')) {
					clientSort($(this));
				} else {
					ajaxSort($(this).attr('href'));
				}
				return false;
			});

		function ajaxSort(url)
		{
			$.ajax({
				url: url,
				success: function(data) {
					$(table).replaceWith( $(data).filter(table) );
				},
				error: function(error) {
					console.log(error);
				}
			});
		}

		function clientSort($button)
		{
			var asc   = !$button.is('.desc'),
				abc   = $button.is('.abc'),
				index = $button.closest('th').index() + 1;

			$(table + ' .sort').removeClass('asc desc');
			$button.addClass(asc ? 'desc' : 'asc');

			var rows   = [],
				$tbody = $(table + ' > tbody');

			$($tbody)
				.find('> tr > td:nth-child(' + index + ')')
				.sort(function(a, b) {
					a = $(a).text();
					b = $(b).text();

					if (!abc) {
						a -= 0;
						b -= 0;
					}

					return a < b
						? -1
						: (a > b ? 1 : 0);
				})
				.map(function(i, el) {
					rows.push( $(el).closest('tr')[0] );
				});

			if (asc) {
				rows.reverse();
			}

			$tbody.empty().append(rows);
		}
	});
</script>
</body>
</html>
