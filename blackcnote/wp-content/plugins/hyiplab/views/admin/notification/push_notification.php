<?php hyiplab_layout('admin/layouts/master'); ?>
<div class="row">
    <div class="col-md-12 mb-30">
        <div class="card bl--5-primary">
            <div class="card-body">
                <p class="text--primary"><?php echo esc_html__('If you want to send signals using push notification by the firebase? Your system must be SSL certified', HYIPLAB_PLUGIN_NAME); ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <form action="<?php echo hyiplab_route_link('admin.setting.notification.template.push.update'); ?>" method="POST">
                <?php hyiplab_nonce_field('admin.setting.notification.template.push.update'); ?>           
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="fw-bold required" for="apiKey"><?php echo esc_html__('API Key', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr('API Key', HYIPLAB_PLUGIN_NAME); ?>" name="apiKey" value="<?php echo esc_attr(@$firebaseConfig->apiKey); ?>" required="" id="apiKey">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="fw-bold required" for="authDomain"><?php echo esc_html__('Auth Domain', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr('Auth Domain', HYIPLAB_PLUGIN_NAME); ?>" name="authDomain" value="<?php echo esc_attr(@$firebaseConfig->authDomain); ?>" required="" id="authDomain">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="fw-bold required" for="projectId"><?php echo esc_html__('Project Id', HYIPLAB_PLUGIN_NAME); ?> </label>
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr('Project Id', HYIPLAB_PLUGIN_NAME); ?>" name="projectId" value="<?php echo esc_attr(@$firebaseConfig->projectId); ?>" required="" id="projectId">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="fw-bold required" for="storageBucket"><?php echo esc_html__('Storage Bucket', HYIPLAB_PLUGIN_NAME); ?> </label>
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr('Storage Bucket', HYIPLAB_PLUGIN_NAME); ?>" name="storageBucket" value="<?php echo esc_attr(@$firebaseConfig->storageBucket); ?>" required="" id="storageBucket">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="fw-bold required" for="messagingSenderId"><?php echo esc_html__('Messaging Sender Id', HYIPLAB_PLUGIN_NAME); ?> </label>
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr('Messaging Sender Id', HYIPLAB_PLUGIN_NAME); ?>" name="messagingSenderId" value="<?php echo esc_attr(@$firebaseConfig->messagingSenderId); ?>" required="" id="messagingSenderId">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="fw-bold required" for="appId"><?php echo esc_html('App Id', HYIPLAB_PLUGIN_NAME); ?></label>
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr('App Id', HYIPLAB_PLUGIN_NAME); ?>" name="appId" value="<?php echo esc_attr(@$firebaseConfig->appId); ?>" required="" id="appId">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="fw-bold required" for="measurementId"><?php echo esc_html__('Measurement Id', HYIPLAB_PLUGIN_NAME)?> </label>
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr( 'Measurement Id', HYIPLAB_PLUGIN_NAME ); ?>" name="measurementId" value="<?php echo esc_attr(@$firebaseConfig->measurementId); ?>" required="" id="measurementId">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="fw-bold required" for="serverKey"><?php echo esc_html__('Server key', HYIPLAB_PLUGIN_NAME)?> </label>
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr__('Server key', HYIPLAB_PLUGIN_NAME); ?>" name="serverKey" value="<?php echo esc_attr(@$firebaseConfig->serverKey); ?>" required="" id="serverKey">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45"><?php echo esc_html__('Submit', HYIPLAB_PLUGIN_NAME); ?></button>
                </div>
            </form>
        </div><!-- card end -->
    </div>
</div>


<div id="pushNotifyModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo esc_html__('Firebase Setup', HYIPLAB_PLUGIN_NAME); ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="steps-tab" data-bs-toggle="tab" data-bs-target="#steps" type="button" role="tab" aria-controls="steps" aria-selected="true"><?php esc_html_e('Steps', HYIPLAB_PLUGIN_NAME); ?></button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="configs-tab" data-bs-toggle="tab" data-bs-target="#configs" type="button" role="tab" aria-controls="configs" aria-selected="false"><?php echo esc_html_e('Configs', HYIPLAB_PLUGIN_NAME); ?></button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="server-tab" data-bs-toggle="tab" data-bs-target="#server" type="button" role="tab" aria-controls="server" aria-selected="false"><?php esc_html_e('Server Key', HYIPLAB_PLUGIN_NAME); ?></button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="steps" role="tabpanel" aria-labelledby="steps-tab">
                        <div class="table-responsive overflow-hidden">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('To Do', HYIPLAB_PLUGIN_NAME); ?></th>
                                        <th><?php echo esc_html_e('Description', HYIPLAB_PLUGIN_NAME); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td data-label="To Do"><?php esc_html_e('Step 1', HYIPLAB_PLUGIN_NAME); ?></td>
                                    <td data-label="Description"><?php esc_html_e('Go to your Firebase account and select', HYIPLAB_PLUGIN_NAME); ?> <span class="text--primary"><?php echo esc_html__('"Go to console', HYIPLAB_PLUGIN_NAME); ?></span>"<?php esc_html_e('and select your project.', HYIPLAB_PLUGIN_NAME); ?></td>
                                </tr>
                                <tr>
                                    <td data-label="To Do"><?php esc_html_e('Step 2', HYIPLAB_PLUGIN_NAME); ?></td>
                                    <td data-label="Description">
                                        <?php esc_html_e('Select Add project and do the following to create your project.', HYIPLAB_PLUGIN_NAME); ?><br>
                                        <code class="text--primary"> <?php esc_html_e('Use the name, Enable Google Analytics, Choose a name and the country for Google Analytics, Use the default analytics settings', HYIPLAB_PLUGIN_NAME); ?></code>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-label="To Do"><?php esc_html_e('Step 3', HYIPLAB_PLUGIN_NAME); ?></td>
                                    <td data-label="Description"><?php esc_html_e('Within your Firebase project, select the gear next to Project Overview and choose Project settings.', HYIPLAB_PLUGIN_NAME); ?></td>
                                </tr>
                                <tr>
                                    <td data-label="To Do"><?php esc_html_e('Step 4', HYIPLAB_PLUGIN_NAME); ?></td>
                                    <td data-label="Description"><?php esc_html_e('Next, set up a web app under the General section of your project settings.', HYIPLAB_PLUGIN_NAME ); ?></td>
                                </tr>
                                <tr>
                                    <td data-label="To Do"><?php esc_html_e('Step 5', HYIPLAB_PLUGIN_NAME); ?></td>
                                    <td data-label="Description"><?php esc_html_e('Next, go to Cloud Messaging in your Firebase project settings and enable Cloud Messaging API.', HYIPLAB_PLUGIN_NAME ); ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade mt-3 ms-2 text-center" id="configs" role="tabpanel" aria-labelledby="configs-tab">
                        <img src="<?php echo esc_url(HYIPLAB_PLUGIN_URL . 'assets/images/default.png') ?>">
                    </div>
                    <div class="tab-pane fade mt-3 ms-2 text-center" id="server" role="tabpanel" aria-labelledby="server-tab">
                        <img src="<?php echo esc_url(HYIPLAB_PLUGIN_URL . 'assets/images/default.png'); ?>">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--dark" data-bs-dismiss="modal"><?php echo esc_html__('Close', HYIPLAB_PLUGIN_NAME); ?></button>
            </div>
        </div>
    </div>
</div>

<?php

$html = '<button class="btn btn-outline--primary btn-sm modalShow" data-type="add" data-bs-toggle="modal" data-bs-target="#addModal"><i class="las la-plus"></i> ' . esc_html__('Add New', HYIPLAB_PLUGIN_NAME) . '</button>';

$html = '<button type="button" data-bs-target="#pushNotifyModal" data-bs-toggle="modal" class="btn btn-outline--info">
<i class="las la-question"></i>' . esc_html__('Help', HYIPLAB_PLUGIN_NAME) . '</button>
<button type="button" class="btn btn-outline--primary testPushNotify">
<i class="las la-bell"></i>' . esc_html__('Test Notification', HYIPLAB_PLUGIN_NAME) . ' </button>';

hyiplab_push_breadcrumb($html);

?>

<script>
    
    (function($){
        function pushNotifyAction(){
            if(!Notification){
                return alert('info', 'Push notifications not available in your browser. Try Chromium.');
            }

            if(Notification.permission === 'denied'){
                alert('info Please allow / reset browser notification');
            }

            if (Notification.permission !== 'granted'){
                Notification.requestPermission();
            }else{
                var notification = new Notification('<?php bloginfo('name'); ?>', {
                    icon: "<?php echo HYIPLAB_PLUGIN_URL ?>assets/global/images/logo_light.png",
                    body: 'Push notification for testing purpose',
                    vibrate: [200, 100, 200]
                });
                notification.onclick = function () {
                    window.open("<?php echo hyiplab_route_link('admin.hyiplab'); ?>");
                };
            }
        }

        $('.testPushNotify').on('click', function(){
            pushNotifyAction();
        });
    })(jQuery);

</script>


<script>
    "use strict";   
    
    var permission = null;
    var authenticated = '1';
    var pushNotify = 0;
    var firebaseConfig = null;

    function pushNotifyAction(){ 
        permission = Notification.permission;

        if(!('Notification' in window)){
            notify('info', 'Push notifications not available in your browser. Try Chromium.')
        }
        else if(permission === 'denied' || permission == 'default'){ //Notice for users dashboard
            $('.notice').append(`
                <div class="col-lg-12">
                    <div class="custom--card mb-4">
                        <div class="card-header justify-content-between d-flex flex-wrap notice_notify">
                            <h5 class="alert-heading">Please Allow / Reset Browser Notification <i class='las la-bell text--danger'></i></h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0 small">If you want to get push notification then you have to allow notification from your browser</p>
                        </div>
                    </div> 
                </div>
            `);
        }
    }

    //If enable push notification from admin panel
    if(pushNotify == 1){
        pushNotifyAction();
    }

    //When users allow browser notification
    if(permission != 'denied' && firebaseConfig){ 
   
        //Firebase 
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        navigator.serviceWorker.register("https://script.viserlab.com/hyiplab/demo/assets/global/js/firebase/firebase-messaging-sw.js")
        
        .then((registration) => {
            messaging.useServiceWorker(registration);
            
            function initFirebaseMessagingRegistration() {
                messaging
                .requestPermission()
                .then(function () {
                    return messaging.getToken()
                })
                .then(function (token){   
                    $.ajax({ 
                        url: 'https://script.viserlab.com/hyiplab/demo/add/device/token',
                        type: 'POST',
                        data: {
                            token: token,
                            '_token': "eN5iqlJchRQ7i06cWOuXv3FiWfBLEYEC6kanNBaW"
                        },
                        success: function(response){
                        },
                        error: function (err) {
                        },
                    });
                }).catch(function (error){
                });
            }

            messaging.onMessage(function (payload){ 
                const title = payload.notification.title;
                const options = {
                    body: payload.notification.body,
                    icon: payload.notification.icon, 
                    click_action:payload.notification.click_action,
                    vibrate: [200, 100, 200]
                };
                new Notification(title, options);
            });

            //For authenticated users
            if(authenticated){
                initFirebaseMessagingRegistration();
            }

        });

    }
</script>