<?php
if(isset($email['content'])){
    $email['content'] = str_replace("\\\"","\"",$email['content']);
} ?>

<?php if( $email['content'] === 'saved-search' && !ulisting_wishlist_active()):?>
    <?php \uListing\Classes\Notices::notice(\uListing\Classes\Notices::TYPE_ERROR, [__("Install <a target='_blank' href='https://stylemixthemes.com/wordpress-classified-plugin/'>uListing Wishlist</a> addon to unlock this feature!", "ulisting")])?>
<?php endif;?>
<style>
    .flex-center {
        display: flex;
        justify-content: center;
    }
</style>
<div id="email-single">
    <h4>{{emailData.title}}</h4>
    <div class="p-t-30">
        <hr>
        <div class="form-group">
            <div class="stm-row">
                <div class="stm-col-3">
                    <label> <?php _e("Email Notification Message", "ulisting"); ?></label>
                </div>
                <div class="stm-col-1">
                    <div  id="email-status">
                        <label class="ulisting-remove-table">
                            <input type="checkbox" v-model="emailData.is_active" @click="toggleCheckbox('is_active')">
                            <div class="ulisting-slider ulisting-round" @click="is_active = !is_active"></div>
                        </label>
                    </div>
                </div>
                <div class="stm-col-3 flex-center">
                    <h6 class="p-t-10">
                        <span v-if="!is_active" class="m-l-15 pull-right"> <span class="badge badge-warning"><?php _e('Inactive', "ulisting")?></span></span>
                        <span v-else class="m-l-15 pull-right"> <span class="badge badge-success"><?php _e('Active', "ulisting")?></span></span>
                    </h6>
                </div>
                <div class="stm-col-3">
                    <div class="p-t-10">
                        <code>
                            <?php echo __(' User enables it to get a brief notification message about the current status.', 'ulisting')?>
                        </code>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="form-group">
            <div class="stm-row">
                <div class="stm-col-3">
                    <label> <?php _e("Logo Image", "ulisting"); ?></label>
                </div>
                <div class="stm-col-1">
                    <div  id="email-status">
                        <label class="ulisting-remove-table">
                            <input type="checkbox" v-model="emailData.header" @click="toggleCheckbox('header')">
                            <div class="ulisting-slider ulisting-round" @click="header = !header"></div>
                        </label>
                    </div>
                </div>
                <div class="stm-col-3 flex-center">
                    <h6 class="p-t-10">
                        <span v-if="!header" class="m-l-15 pull-right"> <span class="badge badge-warning"><?php _e('Disabled', "ulisting")?></span></span>
                        <span v-else class="m-l-15 pull-right"> <span class="badge badge-success"><?php _e('Enabled', "ulisting")?></span></span>
                    </h6>
                </div>
                <div class="stm-col-5">
                    <div class="p-t-10">
                        <code>
                            <?php echo __('User enables it to show his logo image in the header.', 'ulisting')?>
                        </code>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="form-group">
            <div class="stm-row">
                <div class="stm-col-3">
                    <label> <?php _e("Social Contacts", "ulisting"); ?></label>
                </div>
                <div class="stm-col-1">
                    <div  id="email-status">
                        <label class="ulisting-remove-table">
                            <input type="checkbox" v-model="emailData.footer" @click="toggleCheckbox('footer')">
                            <div class="ulisting-slider ulisting-round" @click="footer = !footer"></div>
                        </label>
                    </div>
                </div>
                <div class="stm-col-3 flex-center">
                    <h6 class="p-t-10">
                        <span v-if="!footer" class="m-l-15 pull-right"> <span class="badge badge-warning"><?php _e('Disabled', "ulisting")?></span></span>
                        <span v-else class="m-l-15 pull-right"> <span class="badge badge-success"><?php _e('Enabled', "ulisting")?></span></span>
                    </h6>
                </div>
                <div class="stm-col-3">
                    <div class="p-t-10">
                        <code>
                            <?php echo __('User enables it to put his all social contact links.', 'ulisting')?>
                        </code>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="form-group">
            <div class="stm-row">
                <div class="stm-col-3">
                    <label> <?php _e("Banner Image", "ulisting"); ?></label>
                </div>
                <div class="stm-col-1">
                    <div  id="email-status">
                        <label class="ulisting-remove-table">
                            <input type="checkbox" v-model="emailData.banner" @click="toggleCheckbox('banner')">
                            <div class="ulisting-slider ulisting-round" @click="banner = !banner"></div>
                        </label>
                    </div>
                </div>
                <div class="stm-col-3 flex-center">
                    <h6 class="p-t-10">
                        <span v-if="!banner" class="m-l-15 pull-right"> <span class="badge badge-warning"><?php _e('Disabled', "ulisting")?></span></span>
                        <span v-else class="m-l-15 pull-right"> <span class="badge badge-success"><?php _e('Enabled', "ulisting")?></span></span>
                    </h6>
                </div>
                <div class="stm-col-3">
                    <div class="p-t-10">
                        <code>
                            <?php echo __('User enables it to show a banner image.', 'ulisting')?>
                        </code>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="form-group">
            <div class="stm-row">
                <div class="stm-col-3">
                    <label> <?php _e("Subject", "ulisting")?></label>
                </div>
                <div class="stm-col-5">
                    <div>
                        <input class="form-control" v-model="emailData.subject" type="text">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="stm-row">
                <div class="stm-col-3">
                    <label> <?php _e("Content", "ulisting")?></label>
                </div>
                <div class="stm-col-9">
                    <div>
                            <?php wp_editor( $email['content'], "saved_searches_content", ['textarea_name' => 'UlistingEmail['. esc_attr($email['slug']) .']['.esc_attr($email['content']) .']'] ); ?>
                    </div>

                </div>
            </div>
        </div>
        <button class="btn btn-success" @click="sendEmailData"><?php esc_html_e("Save", "ulisting")?></button>
    </div>

    <hr>
</div>

<script>
    new Vue({
        el: "#email-single",
        data: {
            emailData: {},
            header: Boolean(<?php echo (strval($email['header']) === '1') ? 1 : 0;?>),
            footer: Boolean(<?php echo (strval($email['footer']) === '1') ? 1 : 0;?>),
            banner: Boolean(<?php echo (strval($email['banner']) === '1') ? 1 : 0;?>),
            is_active: Boolean(<?php echo (strval($email['is_active']) === '1') ? 1 : 0;?>),
        },

        mounted() {
            this.emailData = <?php echo json_encode($email)?>;
            this.emailData.header    = this.parseValue(this.emailData.header);
            this.emailData.footer    = this.parseValue(this.emailData.footer);
            this.emailData.banner    = this.parseValue(this.emailData.banner);
            this.emailData.is_active = this.parseValue(this.emailData.is_active);
        },

        methods: {
            parseValue(value) {
                return (value == '1' || value == true)
            },

            toggleCheckbox(type) {
                this.emailData[type] = !this.emailData[type];
            },

            sendEmailData(e) {
                e.preventDefault();
                const iframe = document.querySelector('iframe')
                const iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
                const content = iframeDocument.querySelector('#tinymce')
                if ( content && content.innerHTML)
                    this.emailData.content = content.innerHTML;
                    this.emailData.nonce = ulistingAjaxNonce;
                this.$http.post('ulisting-email/single', this.emailData)
                    .then(response => {
                        if (response && response.body) {
                            location.reload()
                        }
                    })
            }
        }
    });
</script>