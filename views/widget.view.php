<?php declare(strict_types = 0);
/*
** Copyright (C) 2001-2024 Zabbix SIA
**
** This program is free software: you can redistribute it and/or modify it under the terms of
** the GNU Affero General Public License as published by the Free Software Foundation, version 3.
**
** This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
** without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
** See the GNU Affero General Public License for more details.
**
** You should have received a copy of the GNU Affero General Public License along with this program.
** If not, see <https://www.gnu.org/licenses/>.
**/


/**
 * Problem hosts widget view.
 *
 * @var CView $this
 * @var array $data
 */

$table = new CTableInfo();

if ($data['error'] !== null) {
	$table->setNoDataMessage($data['error']);
}
else {
	$sort_div = (new CSpan())->addClass(ZBX_STYLE_ARROW_UP);

	$table
		->setHeader([
			_('Host')
		])
		->setHeadingColumn(0);

	// $table->addRow(new CPre(print_r($data, true)));
	foreach ($data['hosts'] as $host) {
		$table->addRow([
			$host['name']
		]);
	}
}

(new CWidgetView($data))
	->addItem($table)
	->show();
