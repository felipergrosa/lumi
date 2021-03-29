<div class="form-group row" id="cadastro_pedidos_produtos_form2">
                        <div class="col-xs-2 col-md-2">
                            <label>Código</label>
                        </div>
                        <div class="col-xs-3 col-md-3">
                            <label>Nome (Digite para buscar)</label>
                        </div>
                        <div class="col-xs-2 col-md-2">
                            <label>Valor</label>
                        </div>
                        <div class="col-xs-2 col-md-2">
                            <label>Qt</label>
                        </div>
                        <div class="col-xs-2 col-md-2">
                            <label>Subt.</label>
                        </div>
                        <div class="col-xs-1 text-center">
                            <span class="fa fa-trash" style="font-size: 2.2em; cursor:pointer;"></span>
                        </div>
                    </div>
<?php
                        for($i=1; $i<2; $i++){
                            ?>
                            <div class="form-group row" id="dump_produto<?php echo $i; ?>">
                        <div class="col-xs-2 col-md-2">
                            <input type="text" class="form-control" id="vendas_nova_venda_produto_id" name="vendas_nova_venda_produto_id"
                            onkeypress="ProximoCampo('dump_produto<?php echo $i; ?>', 'qt', event);"
                            ondblclick="console.log('dblclick');" readonly
                            />
                        </div>
                        <div class="col-xs-3 col-md-3">
                            <input type="text" class="form-control" id="vendas_nova_venda_produto_nome" name="vendas_nova_venda_produto_nome"
                            onkeyup="BuscaProdutoGatilho(this, this.value, 'dump_produto<?php echo $i; ?>');"
                        />
                        </div>
                        <div class="col-xs-2 col-md-2">
                            <input type="text" class="form-control" id="vendas_nova_venda_produto_valor" name="vendas_nova_venda_produto_valor" readonly/>
                        </div>
                        <div class="col-xs-2 col-md-2">
                            <input type="number" class="form-control" id="vendas_nova_venda_produto_qt" name="vendas_nova_venda_produto_qt" onblur="javascript: RecalculaValor(this, this.value);"
                            onkeypress="ProximoCampo('dump_produto<?php echo $i+1; ?>', 'id', event);"/>
                        </div>
                        <div class="col-xs-2 col-md-2">
                            <input type="text" class="form-control vendas_nova_venda_produto_subtot" id="vendas_nova_venda_produto_subtot" name="vendas_nova_venda_produto_subtot" readonly/>
                        </div>
                        <div class="col-xs-1 text-center">
                            <span class="fa fa-trash" style="font-size: 2.2em; cursor:pointer;" onclick="javascript: DeleteLinha(this);"></span>
                        </div>
                        <div class="col-12">
                            <span class=" font-size-lg">Descontos</span>
                        </div>
                        <!-- <section class="descontos"> -->
                            <div class="col-xs-2 col-md-2">
                                <label for="vendas_nova_venda_produto_desconto_padrao">Padrão</label>
                                <input type="text" class="form-control" id="vendas_nova_venda_produto_desconto_padrao" name="vendas_nova_venda_produto_desconto_padrao" value="0.00" />
                            </div>
                            <div class="col-xs-2 col-md-2">
                                <label for="vendas_nova_venda_produto_desconto_p_comissao">P.Comissão</label>
                                <input type="text" class="form-control" id="vendas_nova_venda_produto_desconto_p_comissao" name="vendas_nova_venda_produto_desconto_p_comissao" value="0.00"/>
                            </div>
                            <div class="col-xs-2 col-md-2">
                                <label for="vendas_nova_venda_produto_desconto_negociacao">Negociacao</label>
                                <input type="text" class="form-control" id="vendas_nova_venda_produto_desconto_negociacao" name="vendas_nova_venda_produto_desconto_negociacao" value="0.00"/>
                            </div>
                            <div class="col-xs-2 col-md-2">
                                <label for="vendas_nova_venda_produto_desconto_antecipado">Antecipado</label>
                                <input type="text" class="form-control" id="vendas_nova_venda_produto_desconto_antecipado" name="vendas_nova_venda_produto_desconto_antecipado" value="0.00"/>
                            </div>
                            <div class="col-xs-2 col-md-2">
                                <label for="vendas_nova_venda_produto_desconto_10_dias">10 Dias</label>
                                <input type="text" class="form-control" id="vendas_nova_venda_produto_desconto_10_dias" name="vendas_nova_venda_produto_desconto_10_dias" value="0.00"/>
                            </div>
                            <div class="col-xs-2 col-md-2">
                                <label for="vendas_nova_venda_produto_desconto_negociado">Negociado</label>
                                <input type="text" class="form-control valor" id="vendas_nova_venda_produto_desconto_negociado" name="vendas_nova_venda_produto_desconto_negociado" value="0.00"/>
                            </div>
                            <div class="col-xs-2 col-md-2">
                                <label for="vendas_nova_venda_produto_desconto_ref_cliente">Ref. Cliente</label>
                                <input type="text" class="form-control" id="vendas_nova_venda_produto_desconto_ref_cliente" name="vendas_nova_venda_produto_desconto_ref_cliente"/>
                            </div>
                            <div class="col-xs-2 col-md-2">
                                <label for="vendas_nova_venda_produto_desconto_pct_ipi">%IPI</label>
                                <input type="text" class="form-control" id="vendas_nova_venda_produto_desconto_pct_ipi" name="vendas_nova_venda_produto_desconto_pct_ipi" value="0.00"/>
                            </div>
                            <div class="col-xs-2 col-md-2">
                                <label for="vendas_nova_venda_produto_desconto_pct_icms">%ICMS</label>
                                <input type="text" class="form-control" id="vendas_nova_venda_produto_desconto_pct_icms" name="vendas_nova_venda_produto_desconto_pct_icms" value="0.00"/>
                            </div>
                        <!-- </section> -->
                    </div>

                    <script type="text/javascript">
                    $(".valor").on("keyup", function (e, v) {
                        console.log('teste')
                        // accept numbers only (0-9)
                        if ((e.which >= 48) && (e.which <= 57)) {

                            // convert CharCode into a number
                            number = String.fromCharCode(e.which);

                            // hide value in input
                            $(this).val("");

                            // main array which holds all numbers
                            value.push(number);

                            // array of numbers before decimal mark
                            before.push(value[i]);

                            // move numbers past decimal mark
                            if (i > 1) {
                                after.push(value[i - 2]);
                                before.splice(0, 1);
                            }

                            // final value
                            var val_final = after.join("") + "." + before.join("");

                            // show value separated by comma(s)
                            $(this).val(addComma(val_final));

                            // update counter
                            i++;

                            // for demo
                            $(".amount").html(" " + $(this).val());

                        } else {

                            // reset values
                            resetVal();
                        }
                    });
                    </script>
                            <?php
                        }
                    ?>
