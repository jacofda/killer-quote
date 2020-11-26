<div class="col-md-6">
    <div class="card card-outline card-warning">
        <div class="card-header">
            <h3 class="card-title">Corpo</h3>
        </div>
        <div class="card-body">

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
                    {!! Form::textarea('descrizione', null, ['class' => 'form-control desc', 'rows' => 2, 'maxlength' => 999]) !!}
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Quantità</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        {!! Form::text('qta', 1, ['class' => 'form-control input-decimal']) !!}
                        <div class="input-group-append">
                            <span class="input-group-text input-group-text-sm" id="basic-addon2">00.00</span>
                        </div>
                    </div>
                    @include('areaseb::components.add-invalid', ['element' => 'qta'])
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 col-xl-7">

                    <div class="form-group row">
                        <label class="col-sm-4 col-xl-7 col-form-label">Prezzo*</label>
                        <div class="col-sm-8 col-xl-5">
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
                        <label class="col-sm-4 col-xl-5 col-form-label">IVA*</label>
                        <div class="col-sm-8 col-xl-7">
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
        <div class="card-footer text-center">
            <button class="btn btn-primary btn-sm btn-block" data-uid="" id="addItem" disabled><i class="fa fa-plus"></i> AGGIUNGI VOCE</button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function() {
            var items = [];
            var itemsFromDB = ($('textarea#itemsToForm').val() != '') ? JSON.parse($('textarea#itemsToForm').val()) : [];

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
                $('input[name="qta"]').val('1.00');
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
                html += '<td>'+item.ivato.toFixed(2)+'</td>';
                html += '<td>'+item.subtotal().toFixed(2)+'</td>';
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
            console.log(itemsFromDB);
            addItemsToTable(itemsFromDB);

            let company = null;
            company = $('select[name="company_id"]').val();
            $('select[name="company_id"]').on('change', function(){
                company = $('select[name="company_id"]').val();
            })


            $("#products").on('select2:select', function(){

                if($('button#addItem').hasClass('edit'))
                {
                    $.get( baseURL+"api/products/"+$(this).find(':selected').val(), function( data ) {
                        $('input.codice').val(data.codice);
                    });
                }
                else
                {
                    $.get( baseURL+"api/products/"+$(this).find(':selected').val(), function( data ) {
                        $('input#prezzo').val(data.prezzo);
                        $('input#perc_iva').val("22");
                        $('input.codice').val(data.codice);
                        $('textarea.desc').val(data.descrizione);
                        $('button#addItem').prop('disabled', false);
                    });
                }

                if(company)
                {
                    $.get( baseURL+"api/companies/"+company+'/discount-exemption', function( data ) {
                        let c_exemption = data.exemption_id;
                        let c_s1 = data.s1;
                        let c_s2 = data.s2;
                        let c_s3 = data.s3;
                        if(c_exemption)
                        {
                            $('select[name="exemption_id"]').val(c_exemption).trigger('change');
                            $('input[name="perc_iva"]').val(0);
                        }
                        if(c_s1)
                        {
                            $('input[name="sconto1"]').val(c_s1);
                        }
                        if(c_s2)
                        {
                            $('input[name="sconto2"]').val(c_s2);
                        }
                        if(c_s3)
                        {
                            $('input[name="sconto3"]').val(c_s3);
                        }
                    });
                }
            });



            $('button#addItem').on('click', function(e){
                e.preventDefault();

                var prezzo = $('input#prezzo').val();

                if(prezzo == '')
                {
                    $('input#prezzo').addClass('is-invalid');
                    alertMe('Il campo prezzo è obbligatorio');
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
                $('input#qta').val(i.qta);
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
                    $('#killerQuoteForm').submit();
                }
                else {
                    console.log('Validation did not pass');
                }
            });


            const validate = () => {
                if(items.length <= 0) {
                    alertMe('Impossibile salvare il preventivo: non hai caricato nessuna voce.');
                    return false;
                }
                return true;
            }

            const alertMe = (str) => {
                new Noty({
                    text: str,
                    type: 'error',
                    theme: 'bootstrap-v4',
                    timeout: 4000,
                }).show();
            }

        })(jQuery);
    </script>
@endpush

