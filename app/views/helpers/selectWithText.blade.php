<div class="form-group">
    <label for="{{ $name }}" class="col-lg-2 control-label">
        {{ $label }}
    </label>
    <div class="col-lg-5">
        {{ Form::select($name, $options, $select_box_default, array_merge(array('class' => 'form-control'), $select_box_attributes)) }}
    </div>
    <div class="col-lg-5">
        {{ Form::text("new_$name", null, array('class' => 'form-control', 'placeholder' => 'Nouveau')) }}
        <a
            href=""
            class="green-link link-in-textbox"
            data-add-to='{{ $name }}'
            @foreach ($new_item_link_attributes as $attribute => $value)
                {{ $attribute }}='{{ $value }}'
            @endforeach
            >
            <span class="glyphicon glyphicon-plus-sign"></span><span class="sr-only">Ajouter une série</span>
        </a>
    </div>
    @if ($select_box_multiple)
        <script type="text/javascript">
            $(document).ready(function() {
                $("select[name='{{ $name }}']").multiselect({
                    maxHeight: 200,
                    buttonWidth: '100%',
                    checkboxName: "{{ $name }}[]",
                    nonSelectedText: 'Aucune sélection',
                    buttonContainer: '<div class="btn-group btn-group-full-width" />',
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    filterPlaceholder: 'Rechercher'
                });
            });
        </script>
    @endif
</div>