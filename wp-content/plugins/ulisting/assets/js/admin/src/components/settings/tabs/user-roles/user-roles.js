import checkbox from '@partials/fields/checkbox'
import input from '@partials/fields/input'
import toggle from '@partials/fields/switch'
import proWrapper from '@partials/blocks/pro-wrapper'
import toggleValidator from '@plugins/toggleValidator'
import postRequest from '@plugins/postRequest'
import draggable from 'vuedraggable'

import {toNumber, renderToast} from "@plugins/index";


import {mapGetters} from 'vuex'

export default {
    props: ['field'],
    data() {
        return {
            count: 0,
            open: false,
            roles: [],
            agency: {},
            user_roles: {},
            edit_panel_id: null,
            user_role_active: false,
            type_list: [
                {
                    id: "text",
                    text: "Text",
                },
                {
                    id: "number",
                    text: "Number",
                },
                {
                    id: "select",
                    text: "Select",
                },
                {
                    id: "checkbox",
                    text: "Checkbox",
                },
                {
                    id: "radio",
                    text: "Radio",
                },
                {
                    id: "textarea",
                    text: "Textarea",
                }
            ],
            role: {
                is_new: 1,
                name: "Simple User",
                slug: "simple_slug",
                custom_fields: [],
                capabilities: {
                    comment: 0,
                    default: false,
                    listing_limit: 0,
                    stm_listing_role: 1,
                    listing_moderation: 0,
                    allow_delete_listings: 0,
                    email_confirmation: 0,
                }
            },
        }
    },

    components: {
        'input-field': input,
        'checkbox-field': checkbox,
        'switch-field': toggle,
        'draggable': draggable,
        'pro-wrapper': proWrapper,
    },

    mounted() {
        if (this.field && typeof this.field.agency !== "undefined")
            this.agency = this.field.agency

        if (this.field && typeof this.field.user_roles !== "undefined")
            this.user_roles = this.field.user_roles

        if (typeof this.user_roles.roles !== "undefined") {
            this.roles = this.user_roles.roles.map(element => {
                for (let key in element.capabilities)
                    element.capabilities[key] = toNumber(element.capabilities[key])
                return element
            })
        }

        if (typeof this.user_roles.uListing_user_role_active !== "undefined")
            this.user_role_active = this.user_roles.uListing_user_role_active

        this.sortRoles()
        this.edit_panel_id = 0
        this.open = true
    },

    computed: {
        ...mapGetters([
            'getAjaxUrl',
            'getNonce',
            'getButtonsText',
            'getGlobalTexts',
        ]),

        text_domains() {
            return this.getGlobalTexts.user_roles || {}
        },

        getToggleData() {
            const result = {value: this.agency.value}
            result.title = toggleValidator(this.agency.value) ? this.agency.enabled : this.agency.disabled;
            return result;
        },

        getActiveStatus() {
            return toNumber(this.user_role_active)
        }
    },

    methods: {
        sortRoles() {
            let roles = JSON.parse(JSON.stringify(this.roles))
            let role = roles.find(role => role.slug === 'agency') || null
            if (role) {
                roles = roles.filter(r => r.slug !== role.slug)
                this.roles = [role, ...roles]
            }
        },

        delete_role(index) {
            const roles = this.clone(this.roles)
            this.roles = roles.filter((_, i) => i !== index)
        },

        check_custom_fields(role) {
            if (!role.custom_fields.length)
                role.custom_fields = [];
        },

        get_role_data_by_key(key) {
            if (typeof this.role[key] !== "undefined")
                return this.role[key]
            return ""
        },

        get_role_capability_by_key(key) {
            if (typeof this.role.capabilities[key] !== "undefined")
                return this.role[key]
            return ""
        },

        show_field_items(field) {
            return (field.type === 'select' || field.type === 'checkbox' || field.type === 'radio')

        },

        removeCustomField(index, custom_field_index) {
            if (typeof this.roles[index] !== "undefined")
                this.roles[index].custom_fields.splice(custom_field_index, 1)
        },

        remove(role_index, index) {
            const roles = this.clone(this.roles)
            const role = roles.find((r, i) => i === role_index)
            if (typeof role !== "undefined") {
                const custom_fields = this.clone(role.custom_fields)
                custom_fields.splice(index, 1)
                role.custom_fields = custom_fields
                roles[role_index] = role
                this.roles = roles
            }
        },

        show_field_panel(element) {
            if (element.is_open) {
                element.is_open = false
                return;
            }
            element.is_open = true
        },

        add_item_field(field) {
            if (+this.user_role_active !== 1)
                return;

            field.items.push({
                slug: "",
                name: ""
            });
        },

        add_custom_fields(role_index) {
            if (+this.user_role_active !== 1)
                return;

            const roles = this.clone(this.roles)
            const role = roles.find((r, i) => i === role_index)
            if (typeof role !== "undefined") {
                role.custom_fields.push({
                    type: "text",
                    name: "",
                    slug: "",
                    required: 1,
                    items: [],
                    is_open: 0
                });

                roles[role_index] = role
                this.roles = roles
            }
        },

        add() {
            if ((+this.user_role_active) !== 1)
                return

            const roles = this.clone(this.roles)
            const role = this.clone(this.role)

            role.capabilities.default = false
            roles.push(role)
            if (roles?.length < 2)
                roles[0].capabilities.default = true

            this.roles = roles

        },

        show_role_panel(index) {
            if (this.edit_panel_id === index) {
                this.edit_panel_id = null;
                return;
            }
            this.edit_panel_id = index;
        },

        updateData(data) {
            if (typeof this.roles[data.index] !== "undefined")
                this.roles[data.index] = data.value ? data.value : this.get_role_data_by_key(data.index)
        },

        updateCapabilityData(data) {
            this.roles = this.roles.map((element, index) => {
                if (index === data.type && typeof element.capabilities !== "undefined") {
                    const value = data.value ? data.value : this.get_role_capability_by_key(data.index)
                    element.capabilities[data.index] = value
                }
                return element
            })
        },

        clone(data) {
            return JSON.parse(JSON.stringify(data))
        },

        changeDefault(event, index) {
            let roles = this.clone(this.roles)
            roles = roles.map(e => {
                e.capabilities.default = false
                return e
            })

            if (typeof roles[index] !== "undefined")
                roles[index].capabilities.default = event.target.checked

            this.roles = roles
            // this.updateCapabilityData(this.roles[index])
        },

        validateSlug(model, value) {
            let name = '';
            for (let i = 0; i < model[value].length; i++) {
                if (
                    (model[value].charCodeAt(i) >= 48 && model[value].charCodeAt(i) <= 57) ||
                    (model[value].charCodeAt(i) >= 97 && model[value].charCodeAt(i) <= 122) ||
                    (model[value].charCodeAt(i) >= 65 && model[value].charCodeAt(i) <= 90) ||
                    model[value].charCodeAt(i) === 32 ||
                    model[value].charCodeAt(i) === 45 ||
                    model[value].charCodeAt(i) === 95
                ) {
                    name += model[value].charAt(i)
                    name = name.replace(/[- ]/g, '_');
                }
            }
            model[value] = name.toLowerCase();

        },

        updateToggleData(data) {
            this.$emit('update', {value: data.value})
            this.agency.value = data.value

            if (this.count !== 0)
                this.agenciesSwitcher()
            this.count++
        },

        agenciesSwitcher() {
            let mode = toggleValidator(this.agency.value)
                ? 'install'
                : 'uninstall'
            postRequest(this.getAjaxUrl, {action: 'stm_agencies_switcher', mode, nonce: this.getNonce}, res => {
                if (res.success) {
                    renderToast(res.message, res.status)
                    if (mode === 'install') {
                        let roles = JSON.parse(JSON.stringify(this.roles))
                        roles = roles.map(role => role)
                        this.edit_panel_id = 0
                        this.roles = [res.role, ...roles]
                    } else
                        this.roles = this.roles.filter(element => element.slug !== 'agency')
                }
            })
        },

        saveRoles() {
            const roles = this.roles
                .map(r => {
                    if (typeof r.custom_fields === "undefined")
                        r.custom_fields = []
                    return r
                })

            postRequest(this.getAjaxUrl, {action: 'stm_save_user_roles', roles, nonce: this.getNonce}, res => {
                if (res.success) {
                    renderToast(res.message, res.status)
                }
            })
        }
    },

    template: `
      <div v-if="open">
      <div class="uListing-info-box">
        <h3 class="uListing-normalize uListing-header-3">{{ agency.title }}</h3>
        <p class="uListing-normalize uListing-note-text" v-html="agency.description"></p>
        <switch-field :field="getToggleData" @update="updateToggleData"></switch-field>
      </div>
      <div class="accordion-container user-roles">
        <div class="accordion-item"
             :class="{'uListing-open': edit_panel_id == index, 'uListing-close': edit_panel_id != index}"
             v-for="(role, index) in roles">
          <div class="accordion-row title">
            <div class="accordion-col">
              <h3 class="uListing-normalize uListing-header-3">{{ role.name }}</h3>
            </div>
            <div class="accordion-col">
              <h4 class="uListing-normalize uListing-header-4">{{ text_domains && text_domains.listing_limit }}:
                <strong>{{ role.capabilities.listing_limit ? role.capabilities.listing_limit : 0 }}</strong></h4>
            </div>
            <div class="accordion-col" style="margin-right: 0; flex: 1 1 34%">
              <div class="accordion-actions">
                <span v-if="role.capabilities && (+role.capabilities.default) === 1"
                      class="uListing-info-status active">{{ text_domains && text_domains.default }}</span>
                <button class="uListing-button uListing-button-text uListing-normalize trash small"
                        :class="{disabled: getActiveStatus !== 1}" @click.prevent="delete_role(index)">
                  {{ text_domains && text_domains.delete }}
                </button>
                <span class="uListing-accordion-toggle" @click="show_role_panel(index)">
										<i v-if="edit_panel_id == index" class="icon--360"></i>
										<i v-if="edit_panel_id != index" class="icon--54"></i>
									</span>
              </div>
            </div>
          </div>

          <template v-if="edit_panel_id == index">
            <div class="accordion-row">
              <div class="accordion-col">
                <div class="uListing-input-field">
                  <span class="uListing-admin-field-title">{{ text_domains && text_domains.name }}</span>
                  <input type="text" v-model="role.name"
                         class="uListing-input uListing-input-text uListing-normalize input-field medium">
                </div>
              </div>
              <div class="accordion-col">
                <div class="uListing-input-field">
                  <span class="uListing-admin-field-title">{{ text_domains && text_domains.listing_limit }}</span>
                  <input type="number" v-model="role.capabilities.listing_limit"
                         class="uListing-input uListing-input-text uListing-normalize input-field medium">
                </div>
              </div>
              <div class="accordion-col">
                <div class="uListing-input-field">
                  <span class="uListing-admin-field-title">{{ text_domains && text_domains.slug }}</span>
                  <input type="text" v-model="role.slug" :class="{disabled: role.slug === 'agency'}"
                         class="uListing-input uListing-input-text uListing-normalize input-field medium">
                </div>
              </div>
            </div>
            <div class="accordion-row" style="margin-bottom: 25px">
              <div class="accordion-col">
                <div class="uListing-checkbox-field">
                  <input type="checkbox" :id="'listing_moderation_' + index" value="1"
                         v-model="role.capabilities.listing_moderation">
                  <label :for="'listing_moderation_' + index">

											<span class="uListing-checkbox-text uListing-normalize">
												{{ text_domains && text_domains.listing_moderation }}
											</span>

                  </label>
                </div>
              </div>
              <div class="accordion-col">
                <div class="uListing-checkbox-field">
                  <input type="checkbox" :id="'default_' + index" value="1" v-model="role.capabilities.default"
                         @change="(e) => changeDefault(e, index)">
                  <label :for="'default_' + index">
											<span class="uListing-checkbox-text uListing-normalize">
												{{ text_domains && text_domains.default_role }}
											</span>
                  </label>
                </div>
              </div>
              <div class="accordion-col" style="flex: 1 1 31%;">
                <div class="uListing-checkbox-field">
                  <input type="checkbox" :id="'comment_' + index" value="1" v-model="role.capabilities.comment">
                  <label :for="'comment_' + index">
											<span class="uListing-checkbox-text uListing-normalize">
												{{ text_domains && text_domains.comment }}
											</span>
                  </label>
                </div>
              </div>
              <div class="accordion-col" style="flex: 1 1 31%;">
                <div class="uListing-checkbox-field">
                  <input type="checkbox" :id="'email_confirmation_' + index" value="1"
                         v-model="role.capabilities.email_confirmation">
                  <label :for="'email_confirmation_' + index">
											<span class="uListing-checkbox-text uListing-normalize">
												{{ text_domains && text_domains.email_confirmation }}
											</span>
                  </label>
                </div>
              </div>
            </div>

            <div class="accordion-row custom-fields">
              <h3 class="uListing-header-3 uListing-normalize">
                {{ text_domains && text_domains.custom_fields }}
              </h3>

              <div class="uListing-draggable-container"
                   :class="{empty: role.custom_fields && role.custom_fields.length <= 0}">
                <draggable v-model="role.custom_fields"
                           :options="{group:'attribute',  animation: 200, disabled: false, handle:'.handle'}">
                  <div class="uListing-draggable-items" v-for="(element, element_index) in role.custom_fields"
                       :key="element_index">
                    <div class="uListing-draggable-panel-items-top">
                      <div class="title">
													<span class="handle">
														<i class="icon--3"></i>
													</span>
                        <p class="uListing-default-text">
                          {{ element.name && element.name.trim().length > 0 ? element.name : 'None' }} </p>
                      </div>
                      <div class="action p-r-10">
													<span class="btn remove" @click="remove(index, element_index)">
														<i class="icon-3096673"></i>
													</span>
                        <span class="btn toggle" v-on:click="show_field_panel(element)">
														<i v-if="element.is_open" class="icon--360"></i>
														<i v-if="!element.is_open" class="icon--54"></i>
													</span>
                      </div>
                    </div>
                    <div class="uListing-draggable-panel-items-inside" v-show="element.is_open">
                      <div class="draggable-row">
                        <div class="draggable-col">
                          <div class="uListing-admin-select">
                            <span
                                class="uListing-admin-field-title">{{ text_domains && text_domains.field_type }}</span>
                            <select class="uListing-select-box uListing-select-box-text uListing-normalize medium"
                                    v-model="element.type">
                              <option v-for="option in type_list" :key="option.id" :value="option.id">
                                {{ option.text }}
                              </option>
                            </select>
                          </div>
                        </div>
                        <div class="draggable-col">
                          <div class="uListing-input-field">
                            <span class="uListing-admin-field-title">{{ text_domains && text_domains.name }}</span>
                            <input type="text" v-model="element.name" placeholder="Name"
                                   class="uListing-input uListing-input-text uListing-normalize input-field medium">
                          </div>
                        </div>
                        <div class="draggable-col" style="flex: 1 1 29%">
                          <div class="uListing-input-field">
                            <span class="uListing-admin-field-title">{{ text_domains && text_domains.slug }}</span>
                            <input type="text" v-model="element.slug" placeholder="Slug"
                                   class="uListing-input uListing-input-text uListing-normalize input-field medium">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div style="display: flex; justify-content: center; align-items: center; margin: 0">
                    <button class="uListing-button uListing-button-text icon green with-icon"
                            :class="{disabled: getActiveStatus !== 1}" @click="add_custom_fields(index)">
                      <i class="icon-992651"></i>
                      {{ text_domains && text_domains.add_field }}
                    </button>
                  </div>
                </draggable>
              </div>
            </div>
          </template>
        </div>
        <div style="display: flex;justify-content: space-between; align-items: center">
          <pro-wrapper v-if="getActiveStatus !== 1" type="with-icon green" :btn="text_domains.add_role"
                       text="User Role Add-on." icon="icon-992651"></pro-wrapper>
          <button v-else class="uListing-button uListing-button-text icon green with-icon" @click="add"
                  style="margin-right: 10px; padding-top: 13px; padding-bottom: 13px">
            <i class="icon-992651"></i>
            {{ text_domains && text_domains.add_role }}
          </button>
          <button class="uListing-button uListing-button-text uListing-normalize icon" @click.prevent="saveRoles">
            {{ getButtonsText.save_roles }}
          </button>
        </div>
      </div>
      </div>
    `
}