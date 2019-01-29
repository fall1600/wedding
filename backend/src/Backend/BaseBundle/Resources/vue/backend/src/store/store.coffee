import Vue from 'vue'
import Vuex from 'vuex'
import storeAlert from './modules/alert.coffee'
import storeBase from './modules/base.coffee'

# 規格元件資料
import storeStyle from './modules/style.coffee'

# 編輯元件資料
import storeEdit from './modules/edit.coffee'

# 檢視元件資料
import storeView from './modules/view.coffee'

# 列表元件資料
import storeList from './modules/list.coffee'

# modal
import storeModal from './modules/modal.coffee'

import storeTitle from './modules/title.coffee'
import storeCategory from './modules/category.coffee'
import storeMenu from './modules/menu.coffee'
import storeCheckboxGroup from './modules/checkboxgroup.coffee'
import storeTemplate from './modules/template.coffee'

Vue.use Vuex

export default new Vuex.Store {
  modules:
    alert: storeAlert
    base:  storeBase
#    style: storeStyle
    edit: storeEdit
    view: storeView
    list: storeList
    modal: storeModal
    title: storeTitle
    category: storeCategory
    menu: storeMenu
    checkboxgroup: storeCheckboxGroup
    template: storeTemplate
}
