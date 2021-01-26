<div class="col-md-12">
    <div class="card card-outline card-warning">
        <div class="card-header">
            <h3 class="card-title">Corpo</h3>
        </div>
        <div class="card-body pb-0">

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Prodotto*</label>
                        <div class="col-sm-8">
                            {!! Form::select('product_id', $products, null, ['class' => 'form-control select2bs4', 'data-placeholder' => 'Seleziona Prodotto', 'id' => 'products', 'data-fouc', 'style' => 'width:100%']) !!}
                        </div>
                    </div>
                    <input type="hidden" name="codice" class="codice" value="">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Descrizione</label>
                        <div class="col-sm-8">
                            {!! Form::textarea('descrizione', null, ['class' => 'form-control desc', 'rows' => 1, 'maxlength' => 999]) !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">

                    <div class="row">

                        <div class="col-sm-12 col-xl-7">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Quantità</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        {!! Form::text('qta', 1, ['class' => 'form-control input-decimal', 'id' => 'qp']) !!}
                                        <div class="input-group-append">
                                            <span class="input-group-text input-group-text-sm" id="basic-addon2">00.00</span>
                                        </div>
                                    </div>
                                    @include('areaseb::components.add-invalid', ['element' => 'qta'])
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-xl-5">

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Sconto</label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col">
                                            <div class="input-group">
                                                {!! Form::text('sconto', null, ['class' => 'form-control input-decimal']) !!}
                                                <div class="input-group-append">
                                                    <span class="input-group-text input-group-text-sm" id="basic-addon2">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-12 col-xl-7">

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Prezzo*</label>
                                <div class="col-sm-8">
                                    <div class="input-group xl-ml-5">
                                        {!! Form::text('prezzo', null, ['class' => 'form-control input-decimal', 'id' => 'prezzo']) !!}
                                        <div class="input-group-append">
                                            <span class="input-group-text input-group-text-sm" id="basic-addon2">00.00€</span>
                                        </div>
                                    </div>
                                    @include('areaseb::components.add-invalid', ['element' => 'prezzo'])
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-xl-5">

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">IVA*</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        {!! Form::text('perc_iva', null, ['class' => 'form-control input-decimal', 'id' => 'perc_iva']) !!}
                                        <div class="input-group-append">
                                            <span class="input-group-text input-group-text-sm" id="basic-addon2">00.00%</span>
                                        </div>
                                    </div>
                                    @include('areaseb::components.add-invalid', ['element' => 'perc_iva'])
                                </div>
                            </div>

                        </div>
                    </div>


                </div>

            </div>
        </div>
        <div class="card-footer text-center">
            <button class="btn btn-primary btn-sm btn-block" data-uid="" id="addItem" disabled><i class="fa fa-plus"></i> AGGIUNGI VOCE</button>
        </div>
    </div>
</div>

@push('scripts')
    <script>

        (function() {
            var itemsChildren = [];
            var items = [];
            var itemsFromDB = ($('textarea#itemsToForm').val() != '') ? JSON.parse($('textarea#itemsToForm').val()) : [];

            const getExtra = (response) => {
                extra = {};
                extra.c_exception = response.exemption_id+"";
                extra.c_s1 = response.s1;
                extra.c_s2 = response.s2;
                extra.c_s3 = response.s3;
                extra.locale = response.lingua;
                return extra;
            };


            const processResult = (response, extra) => {
                $('input#prezzo').val(response.prezzo);
                $('input.codice').val(response.codice);
                $('textarea.desc').val(response.descrizione);
                $('button#addItem').prop('disabled', false);

                if(extra.c_exception !== null)
                {
                    $('input#perc_iva').val(0);
                }
                else
                {
                    $('input#perc_iva').val("22");
                }
                if(extra.c_s1)
                {
                    $('input[name="sconto"]').val(extra.c_s1);
                }
            }

            const addChildrenItems = (element) => {
                item = new Item(
                        element.product_id,
                        element.product.nome,
                        element.product.codice,
                        element.descrizione,
                        element.product.prezzo,
                        element.perc_iva,
                        parseInt(element.qta),
                        1 - (element.sconto/100),
                        element.sconto);
                itemsChildren.push(item);
            }

            class Item
            {
                constructor(id, nome, codice, descrizione, prezzo, perc_iva, qta, sconto, perc_sconto)
                {
                    this.uid = Math.random().toString(36).substr(2, 5);
                    this.id = id;
                    this.nome = nome;
                    this.codice = codice;
                    this.descrizione = descrizione;
                    this.prezzo = parseFloat(prezzo);
                    this.perc_iva = parseInt(perc_iva);
                    this.sconto = sconto != 1 ? parseFloat(prezzo)*sconto : null;
                    this.perc_sconto = sconto != 1 ? parseFloat(perc_sconto) : null;
                    this.qta = parseFloat(qta).toFixed(2);
                    this.ivato = (sconto != 1) ? (parseFloat(prezzo)*sconto) * parseFloat(qta) * (parseInt(perc_iva)/100) : parseFloat(prezzo) * parseFloat(qta) * (parseInt(perc_iva)/100);
                }

                subtotal()
                {
                    if(this.sconto == null)
                    {
                        return  (parseFloat(this.prezzo) * parseFloat(this.qta)) + parseFloat(this.ivato);
                    }
                    return ( parseFloat(this.sconto) * parseFloat(this.qta) ) + parseFloat(this.ivato);
                }
            }

            const resetItemForm = () => {
                $('#products').select2().val(null).trigger('change');
                $('textarea.desc').val('');
                $('input#prezzo').val('');
                $('input#perc_iva').val('');
                $('input.codice').val('');
                $('input#qp').val('1.00');
                $('input[name="sconto"]').val('');
                $('select[name="exemption_id"]').select2().val(null).trigger('change');
                let btn = $('button#addItem');
                btn.prop('disabled', true);
                btn.html('<i class="fa fa-plus"></i> AGGIUNGI VOCE');
                $('button#save').prop('disabled', false);
                if(btn.hasClass('edit'))
                {
                    btn.removeClass('edit');
                }
            }

            const addItemToTable = (item) => {
                html = '<tr class="prodRowId-'+item.uid+'">';
                html += '<td class="pl-2">'+item.codice+'</td>';
                html += '<td>'+item.descrizione+'</td>';
                html += '<td>'+item.qta+'</td>';
                if(item.sconto != null)
                {
                    html += '<td>'+(item.sconto.toFixed(2))+'</td>';
                }
                else
                {
                    html += '<td>'+item.prezzo.toFixed(2)+'</td>';
                }
                if(item.perc_sconto != null)
                {
                    html += '<td>'+(item.perc_sconto.toFixed(2))+'</td>';
                }
                else
                {
                    html +='<td></td>';
                }
                html += '<td class="pr-2">';
                html += '<a href="#" class="btn btn-sm removeProdRow" id="prodId-'+item.uid+'"><span class="text-danger"><i class="fa fa-trash"></i></span></a>';
                html += '<a href="#" class="btn btn-sm editProdRow" id="prodId-'+item.uid+'"><span class="text-warning"><i class="fa fa-edit"></i></span></a>';
                html += '</td>';
                html += '</tr>';
                $('.table.voci tbody').append(html);
                resetItemForm();
            }

            const addItemsToTable = (r) => {
                if(Object.entries(r).length !== 0)
                {
                    Object.entries(r).forEach(([key, item]) => {
                        let newItem = {};
                        var sconto = item.sconto == 0 ? 1 : (1-item.sconto/100);
                        var perc_sconto = item.sconto == 0 ? 0 : item.sconto;
                        newItem = new Item(
                            item.product_id,
                            item.product.nome,
                            item.product.codice,
                            item.descrizione,
                            item.importo,
                            item.perc_iva,
                            item.qta,
                            sconto,
                            perc_sconto,
                        );

                        items.push(newItem);
                        addItemToTable(newItem);
                    });
                }
            }
            addItemsToTable(itemsFromDB);

            let company = null;let extra = {};
            company = $('select[name="company_id"]').val();

            $('select[name="company_id"]').on('change', function(){
                company = $('select[name="company_id"]').val();
            });



            $("#products").on('select2:select', function(){
                let prod_id = $(this).find(':selected').val();

                if($('select[name="company_id"]').val() == "")
                {
                    err("Devi prima selezionare un'azienda")
                    resetItemForm();
                    return false;
                }

                if($('button#addItem').hasClass('edit'))
                {
                    $.get( baseURL+"api/products/"+$(this).find(':selected').val(), function( data ) {
                        $('input.codice').val(data.codice);
                    });
                }
                else
                {
                    axios.get( baseURL+'api/companies/'+company+'/discount-exemption').then(function(resp1){

                        //get extra info from company
                        extra = getExtra(resp1.data);

                        axios.get( baseURL+'api/products/'+prod_id+'/'+extra.locale ).then(function(resp2){

                            //show process inf in table
                            processResult(resp2.data, extra)

                            if(resp2.data.children !== null)
                            {
                                axios.get( baseURL+"api/products/"+prod_id+"/children/"+company ).then(function(resp3){

                                    //load children
                                    resp3.data.forEach(function(element){
                                        addChildrenItems(element)
                                    });

                                });
                            }

                        });
                    });
                }
            });

            $('button#addItem').on('click', function(e){
                e.preventDefault();

                var prezzo = $('input#prezzo').val();

                if(prezzo == '')
                {
                    $('input#prezzo').addClass('is-invalid');
                    err('Il campo prezzo è obbligatorio');
                    $('input#prezzo').on('focusin', function(){
                        $(this).removeClass('is-invalid');
                    });
                    return false;
                }

                var select = $('#products').select2('data');
                var desc = $('textarea.desc').val();
                var perc_iva = $('input#perc_iva').val();
                var qta = $('input[name="qta"]').val() ? $('input[name="qta"]').val() : '1.00';
                var sconto = (1-(parseInt($('input[name="sconto"]').val() ? $('input[name="sconto"]').val() : 0)/100));
                var perc_sconto = ((1-sconto)*100).toFixed(2);
                var codice = $('input.codice').val();

                if($(this).hasClass('edit'))
                {
                    let newItem = new Item(select[0].id, select[0].text, codice, desc, prezzo, perc_iva, qta, sconto, perc_sconto);
                    items = items.filter(item => item.uid != $(this).attr('data-uid'));
                    items.push(newItem);
                    addItemToTable(newItem);
                    $('a#prodId-'+$(this).attr('data-uid')).trigger('click');
                }
                else
                {
                    item = new Item(select[0].id, select[0].text, codice, desc, prezzo, perc_iva, qta, sconto, perc_sconto);
                    items.push(item);
                    addItemToTable(item);
                    Object.entries(itemsChildren).forEach(([key, elem]) => {
                        items.push(elem);
                        addItemToTable(elem);
                    });
                    itemsChildren = [];

                }
            });


            $('table.table.voci').on('click', 'a.removeProdRow', function(e){
                e.preventDefault();
                var uid = $(this).attr('id').replace('prodId-', '');
                $(this).parent('td').parent('tr').remove();
                items = items.filter(item => item.uid != uid);
            });

            $('table.table.voci').on('click', 'a.editProdRow', function(e){
                e.preventDefault();
                var uid = $(this).attr('id').replace('prodId-', '');
                var i = items.filter(item => item.uid == uid)[0];
                $('input[name="codice"]').val(i.codice);
                $('#products').select2().val(i.id).trigger('change');
                $('textarea.desc').val(i.descrizione);
                $('input#perc_iva').val(i.perc_iva);
                $('input[name="qta"]').val(parseFloat(i.qta).toFixed(2));
                $('input#prezzo').val(i.prezzo);

                if(i.perc_sconto)
                {
                    $('input[name=sconto]').val(i.perc_sconto);
                }
                let btn = $('button#addItem');
                btn.prop('disabled', false);
                btn.html('<i class="fa fa-plus"></i> MODIFICA VOCE');
                btn.addClass('edit');
                btn.attr('data-uid', uid);
            });


            $('button#save').on('click', function(e){
                e.preventDefault();

                if(validate())
                {
                    $('textarea#itemsToForm').html(JSON.stringify(items));
                    $('#orderConfirmationForm').submit();
                }
                else {
                    console.log('Validation did not pass');
                }
            });


            const validate = () => {
                if(items.length <= 0) {
                    err('Impossibile salvare il preventivo: non hai caricato nessuna voce.');
                    return false;
                }
                return true;
            }


        })(jQuery);
    </script>
@endpush
