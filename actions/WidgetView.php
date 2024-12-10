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


namespace Modules\MyHosts\Actions;

use API,
	CArrayHelper,
	CControllerDashboardWidgetView,
	CControllerResponseData;

class WidgetView extends CControllerDashboardWidgetView {

	protected function doAction(): void {
		$data = [
			'name' => $this->getInput('name', $this->widget->getDefaultName()),
			'error' => null,
			'user' => [
				'debug_mode' => $this->getDebugMode()
			]
		];

		// Editing template dashboard?
		if ($this->isTemplateDashboard() && !$this->fields_values['override_hostid']) {
			$data['error'] = _('No data.');
		}
		else {
			$filter_groupids = !$this->isTemplateDashboard() && $this->fields_values['groupids']
				? getSubGroups($this->fields_values['groupids'])
				: null;

			if ($this->isTemplateDashboard()) {
				$filter_hostids = $this->fields_values['override_hostid'];
			}
			else {
				$filter_hostids = $this->fields_values['hostids'] ?: null;
			}

			// Get host groups.
			$groups = API::HostGroup()->get([
				'output' => ['groupid', 'name'],
				'groupids' => $filter_groupids,
				'hostids' => $filter_hostids,
				'with_monitored_hosts' => true,
				'preservekeys' => true
			]);
			CArrayHelper::sort($groups, ['name']);

			// Get hosts.
			$hosts = API::Host()->get([
				'output' => ['hostid', 'name', 'maintenanceid', 'maintenance_status', 'maintenance_type'],
				'selectHostGroups' => ['groupid'],
				'groupids' => array_keys($groups),
				'hostids' => $filter_hostids,
				'filter' => [
					'maintenance_status' => null
				],
				'tags' => $this->fields_values['tags'] ?: null,
				'monitored_hosts' => true,
				'preservekeys' => true
			]);

			$data += [
				'filter' => [
					'hostids' => $this->isTemplateDashboard()
						? $this->fields_values['override_hostid']
						: $this->fields_values['hostids']
				],
				'hosts' => $hosts,
				'groups' => $groups
			];
		}

		// Pass results to view.
		$this->setResponse(new CControllerResponseData($data));
	}
}
