@extends('admin')

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h2>{{ trans('category.sorting.sort_categories') }}</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="category-sorting-tree-wrapper">
                                <button class="btn btn-primary" id="save-tree-button">{{ trans('category.sorting.save_changes') }}</button>
                                <div class="category-sorting-tree">
                                    {!! category_menu_tree($categories_tree) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <h2>{{ trans('category.sorting.help') }}</h2>
                            <p>{{ trans('category.sorting.help_text1') }}</p>
                            <p>{{ trans('category.sorting.help_text2') }}</p>
                            <p>{{ trans('category.sorting.help_text3') }}</p>
                            <p><b style="text-transform: uppercase;">{{ trans('category.sorting.its_recommended') }}</b>&nbsp;
                            {{ trans('category.sorting.sort_after_import') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
    <script>
        $(function(){

            var currentlyDragging = null;

            $('.category-sorting-tree').jstree({
                core: {
                    check_callback: 'true'
                },
                plugins: ['dnd']
            });

            var nodesArray = [];

            var walkTree = function(node, parent_id, left, position){

                left = undefined == left ? 1 : left;
                parent_id = parent_id == undefined ? null : parent_id;

                var right = left + 1;

                var children = node.children;

                if (children.length > 0){

                    for (var i in children){

                        right = walkTree(children[i], undefined !== node.data ? node.data.cid : null, right, i);

                    }

                }
                if (undefined !== node.data && node.data.cid){
                    nodesArray.push({
                        id: node.data.cid,
                        parent_id: parent_id,
                        left: left,
                        right: right,
                        pos: parseInt(position) + 1
                    });
                }
                return right + 1;
            };

            $(document).on('click', '#save-tree-button', function(e){

                e.preventDefault();

                var $tree = $('.category-sorting-tree');

                var nodes = $tree.jstree().get_json($tree, {});

                var tree_nodes = {
                    children: nodes
                };

                nodesArray = [];

                walkTree(tree_nodes);

                var btn = this;

                $(btn).text("{{ trans('category.sorting.saving') }}");

                $.post('{{ prefixed_route('/admin/categories/update-sorting') }}', {nodes: JSON.stringify(nodesArray)})
                        .success(function(res){
                            if (res.status == 'success') {
                                bootbox.alert("{{ trans('category.sorting.categories_updated') }}");
                            } else {
                                bootbox.alert("{{ trans('category.sorting.categories_sort_error') }}");
                            }
                        })
                        .fail(function(){
                            bootbox.alert("{{ trans('category.sorting.categories_sort_error') }}");
                        })
                        .done(function(){
                            $(btn).text("{{ trans('category.sorting.save_changes') }}");
                        });

            });
        });
    </script>
@endsection