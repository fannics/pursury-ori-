@extends('admin')

@section('main_content')

    <div class="row menu-configuration-page">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h2>Menú principal</h2>
                            <p>
                                Configure el menú principal del sistema frontal
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <h4>Elementos del menú</h4>
                            <table class="table table-bordered table-striped table-condensed menu-definition" data-action-url="{{ route('admin_front_menu_handle') }}">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th class="text-center">Mostrar hijos</th>
                                        <th class="text-center">Orden</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="no-items-row">
                                        <td colspan="4" class="text-center">No tiene elementos en su menú principal</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <a href="#" class="add-item"><i class="fa fa-plus"></i> Nuevo elemento</a>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-sm-8">
                            <h4>Preview</h4>
                            <nav class="navbar navbar-default navbar-preview">
                                <div class="container-fluid">
                                    <div class="navbar-header">
                                        <a class="navbar-brand" href="{{ route('homepage') }}">
                                            <img src="{{asset(settings('app.route_prefix').'/images/watermarksincom_resized.png')}}" alt="Entrenamiento">
                                        </a>
                                    </div>
                                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                                        <ul class="nav navbar-nav navbar-items">
                                        </ul>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="new-item-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Agregar elemento de menú</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <form id="add-item-form">
                                <div class="form-group">
                                    <label for="category">Categoría</label>
                                    <select name="category" id="category" class="form-control">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">
                                        <input type="checkbox" name="display_children" id="display-children" value="1" /> Mostrar hijos
                                    </label>
                                </div>
                                <div class="error">
                                    <p class="error"></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary add-item-submit">Guardar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
    <script>
        window.menu_definition = {!! $menu_definition_json !!} ;
    </script>
@endsection
