import app from '../../admin/app';
import DashboardWidget from './DashboardWidget';
import listItems from '../../common/helpers/listItems';
import ItemList from '../../common/utils/ItemList';
import Dropdown from '../../common/components/Dropdown';
import Button from '../../common/components/Button';
import LoadingModal from './LoadingModal';

export default class StatusWidget extends DashboardWidget {
  className() {
    return 'StatusWidget';
  }

  content() {
    return <ul>{listItems(this.items().toArray())}</ul>;
  }

  items() {
    const items = new ItemList();

    items.add(
      'tools',
      <Dropdown
        label={app.translator.trans('core.admin.dashboard.tools_button')}
        icon="fas fa-cog"
        buttonClassName="Button"
        menuClassName="Dropdown-menu--right"
      >
        {this.toolsItems().toArray()}
      </Dropdown>
    );

    items.add('version-duroom', [<strong>DuRoom</strong>, <br />, app.forum.attribute('version')]);
    items.add('version-php', [<strong>PHP</strong>, <br />, app.data.phpVersion]);
    items.add('version-mysql', [<strong>MySQL</strong>, <br />, app.data.mysqlVersion]);

    return items;
  }

  toolsItems() {
    const items = new ItemList();

    items.add(
      'clearCache',
      <Button onclick={this.handleClearCache.bind(this)}>{app.translator.trans('core.admin.dashboard.clear_cache_button')}</Button>
    );

    return items;
  }

  handleClearCache(e) {
    app.modal.show(LoadingModal);

    app
      .request({
        method: 'DELETE',
        url: app.forum.attribute('apiUrl') + '/cache',
      })
      .then(() => window.location.reload());
  }
}
