angular.module('HomepageEdit', ['ui.bootstrap', 'color.picker'])
    .config(['$interpolateProvider', function($interpolateProvider){

        $interpolateProvider.startSymbol('<%');
        $interpolateProvider.endSymbol('%>');

    }])
    .controller('MainController', ['$scope', '$http', '$modal', function($scope, $http, $modal){

        $scope.home_definition = {};

        $http.get(window.app_prefix + '/admin/homepage/definition')
            .success(function(res){

                $scope.home_definition = res.data;
                $scope.theme_id = res.id;
                $scope.theme = res.data.theme;

            })
            .error(function(){

            });

        $scope.button_edit_action = null;
        $scope.button_edit_source = null;

        var saveDefinition = function(){
            $http.post(window.app_prefix + '/admin/homepage/update', {data: $scope.home_definition, theme_id: $scope.theme_id})
                .success(function(res){
                    document.getElementById('the-frame').contentDocument.location.reload(true);
                })
                .error(function(){

                });
        };

        $scope.editLinkModal = function(link, source, max){

            if (link){



                $scope.selected_button = link;
                $scope.button_edit_action = 'edit';


            } else {

                if (undefined !== $scope.home_definition.home_top && undefined !== $scope.home_definition.home_top.buttons && $scope.home_definition.home_top.buttons.length >= max){
                    bootbox.alert('Solo puede agregar ' + max + ' botones');
                    return;
                }

                $scope.selected_button = {
                    title: '',
                    text: '',
                    url: ''

                };

                $scope.button_edit_action = 'create';
            }

            $scope.button_edit_source = source;

            var modalInstance = $modal.open({
                animation: true,
                template: angular.element(document.getElementById('myModalContent')).html(),
                controller: 'ButtonModalController',
                size: 'md',
                backdrop: 'static',
                keyboard: false,
                resolve: {
                    selected_button: function(){ return $scope.selected_button; }
                }
            });

            modalInstance.result.then(function(res){

                if (res && $scope.button_edit_action && $scope.button_edit_source){

                    if ($scope.button_edit_action == 'create'){
                        $scope.home_definition[$scope.button_edit_source].buttons.push(res);
                    }

                    saveDefinition();

                }
            }, function(){

            });
        };

        $scope.removeButton = function(source, index){

            $scope.home_definition[source].buttons.splice(index,1);

            saveDefinition();

        };

        $scope.navigationModal = function (source, levels){

            var navigationModalInstance = $modal.open({
                animation: true,
                template: angular.element(document.getElementById('navigationModalContent')).html(),
                controller: 'NavigationMakerController',
                size: 'lg',
                backdrop: 'static',
                keyboard: false,
                resolve: {
                    navigation: function(){
                        switch(source){
                            case 'navigation':
                                return $scope.home_definition.navigation;
                                break;
                            case 'home_bottom.navigation':
                                return $scope.home_definition.home_bottom.navigation;
                                break;
                        }

                    },
                    navigation_levels: function(){ return levels; }
                }
            });

            navigationModalInstance.result.then(function(res){

                switch(source){
                    case 'navigation':
                        $scope.home_definition.navigation = res;
                        break;
                    case 'home_bottom.navigation':
                        $scope.home_definition.home_bottom.navigation = res;
                        break;
                }

                saveDefinition();

            }, function(){

            });
        };

        $scope.available_translations = [
        ];

        $scope.toggleTrans = function(t){

            var index = $scope.home_definition.footer.translations.indexOf(t.value);

            if (index > -1){

                $scope.home_definition.footer.translations.splice(index, 1);

            } else {
                $scope.home_definition.footer.translations.push(t.value);
            }

            saveDefinition();

        };

        $scope.transEnabled = function(t){
            for (var i in $scope.home_definition.footer.translations){
                if ($scope.home_definition.footer.translations[i].value == t.value){
                    return true;
                }
            }
            return false;
        };

        $scope.saveChanges = function(){

            $scope.home_definition.theme = $scope.theme;
            saveDefinition();
            
        };

        $('#home_background').fileupload({
            url: window.app_prefix + '/admin/homepage/home-background',
            dataType: 'json',
            send: function(e, data){
                var elem = $('#background-upload-btn > span');
                var text = elem.text();
                elem.text(elem.attr('data-loading-text'));
                elem.attr('data-loading-text', text);
            },
            done: function (e, data) {
                alert('done')
            },
            always: function(e, data){
                var elem = $('#background-upload-btn > span');
                var text = elem.text();
                elem.text(elem.attr('data-loading-text'));
                elem.attr('data-loading-text', text);
            }
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');

        $('#logo_upload').fileupload({
            url: window.app_prefix + '/admin/homepage/logo',
            dataType: 'json',
            send: function(e, data){
                var elem = $('#logo-upload-btn > span');
                var text = elem.text();
                elem.text(elem.attr('data-loading-text'));
                elem.attr('data-loading-text', text);
            },
            done: function (e, data) {
                alert('done')
            },
            always: function(e, data){
                var elem = $('#logo-upload-btn > span');
                var text = elem.text();
                elem.text(elem.attr('data-loading-text'));
                elem.attr('data-loading-text', text);
            }
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');

        $('#small_logo_upload').fileupload({
            url: window.app_prefix + '/admin/homepage/small-logo',
            dataType: 'json',
            send: function(e, data){
                var elem = $('#logo-small-upload-btn > span');
                var text = elem.text();
                elem.text(elem.attr('data-loading-text'));
                elem.attr('data-loading-text', text);
            },
            done: function (e, data) {
                alert('done')
            },
            always: function(e, data){
                var elem = $('#logo-small-upload-btn > span');
                var text = elem.text();
                elem.text(elem.attr('data-loading-text'));
                elem.attr('data-loading-text', text);
            }
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');


    }])
    .controller('ButtonModalController', ['$scope', '$modalInstance', '$timeout', '$http', 'selected_button', function($scope, $modalInstance, $timeout, $http, selected_button){

        $scope.selected_button = selected_button;

        $scope.ok = function(){

            $scope.show_url_error = false;

            if ($scope.button_form.$valid){
                // $modalInstance.close($scope.selected_button);
                $modalInstance.close($scope.selected_button);
            }

        };

    }])
    .controller('NavigationMakerController', ['$scope', '$modalInstance', 'navigation', '$http', '$timeout', 'navigation_levels',function($scope, $modalInstance, navigation, $http, $timeout, navigation_levels){

        $scope.selected_node = [];

        $scope.items_receiver = null;

        $scope.navigation = angular.copy(navigation);

        $scope.categories = null;

        $http.get(window.app_prefix + '/admin/homepage/categories')
            .success(function(res){
                $scope.categories = res.categories;
            })
            .error(function(){

            });

        $scope.addNodeAction = function(list){

            $scope.show_form = true;
            $scope.reset($scope.add_node);
            $scope.items_receiver = list;
            $scope.form_action = 'add';

        };

        $scope.reset = function(form) {
            if (form) {
                form.$setPristine();
                form.$setUntouched();
            }
            $scope.new_node = {};
            $scope.selecte_category = {};
        };

        $scope.createNode = function(node_type){
            if ($scope.add_node.$valid){

                if ($scope.form_action == 'add'){
                    var elem = angular.copy($scope.new_node);
                    $scope.show_form = false;
                    $scope.items_receiver.push(elem);
                } else {
                    $scope.selected_item = $scope.new_node;
                }
            }
        };

        $scope.$watch('selected_category', function(){
            if ($scope.selected_category){
                $scope.new_node = {
                    url: $scope.selected_category.url,
                    title: $scope.selected_category.text,
                    text: $scope.selected_category.text
                };
            }
        });

        $scope.selected_indexes = [];

        $scope.form_action = 'add';

        $scope.selected_item = null;

        $scope.selectNode = function(list, node, index){

            if (list <= navigation_levels - 1){
                $scope.new_node = node;

                $scope.show_form = true;

                $scope.form_action = 'edit';

                $scope.selected_item = node;

                if (node.children == undefined){
                    node.children = [];
                }

                if (list == 0){
                    $scope.selected_node[1] = null;
                    $scope.selected_node[2] = null;
                }

                $scope.selected_node[list] = node;
                $scope.selected_indexes[list] = index;

                $scope.remove_confirm = false;

            }
        };

        $scope.saveNavigation = function(){
            $modalInstance.close($scope.navigation);
        };

        $scope.isNodeActive = function(list, index){
            if (undefined !== $scope.selected_indexes[list]){
                return $scope.selected_indexes[list] == index;
            }
            return false;
        };

        $scope.childrenCount = function(node){
            return undefined !== node.children ? node.children.length.toString() : '0';
        };

        $scope.removeConfirm = function(list, index){
            $scope.remove_confirm = true;
            $scope.remove_from_list = list;
            $scope.remove_index = index;
        };

        $scope.removeNode = function(){
            $scope.remove_from_list.splice($scope.remove_index, 1);
            $scope.cancelRemove();
        };

        $scope.cancelRemove = function(){
            $scope.remove_confirm = false;
            $scope.remove_from_list = null;
            $scope.remove_index = null;
        };

    }]);
