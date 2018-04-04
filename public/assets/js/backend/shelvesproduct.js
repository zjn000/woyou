define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'shelvesproduct/index',
                    add_url: 'shelvesproduct/add',
                    edit_url: 'shelvesproduct/edit',
                    del_url: 'shelvesproduct/del',
                    multi_url: 'shelvesproduct/multi',
                    table: 'shelves_product',
                }
            });

            var table = $("#table");
            var attr = table.data("shelves-id");
            var extend_url = '';
            var extend_s = "LIKE";
            if(attr != ''){
                extend_url = '/shelves_id/'+attr;
                extend_s = false;
            }

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url+extend_url,
                pk: 'id',
                sortName: 'status',
                sortOrder:'asc',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'shelves.name', title: __('Shelves_name'),operate:extend_s},
                        {field: 'lv', title: __('Lv'),
                            searchList:{
                                "1":'第1层',"2":'第2层',"3":'第3层',
                                "4":'第4层',"5":'第5层',"6":'冰箱1层',
                                "7":'冰箱2层',"8":'冰箱3层',"9":'冰箱4层'
                            }
                        },
                        {field: 'product.name', title: __('Product_name'),operate:'LIKE', formatter: Controller.api.formatter.pname},
                        {field: 'product.barcode', title: __('Product_barcode')},
                        {field: 'product.image', title: __('Product_image'),operate: false, formatter: Table.api.formatter.image},
                        {field: 'product.price', title: __('Product_price'),operate: false},
                        {field: 'status', title: __('Status'),operate: false, formatter: Controller.api.formatter.status},
                        {field: 'standard_number', title: __('Standard_number'),operate: false},
                        {field: 'num', title: __('Num'),operate: false},
                        {field: 'should_replenish', title: __('Should_replenish'),operate: false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                //启用普通表单搜索
                search: false,
            });


            if(/iPhone|iPad|iPod|iOS/i.test(navigator.userAgent)) {
                var i=0;
                table.on('click-row.bs.table', function (e, row, element, field) {
                    i++;
                    setTimeout(function () {
                        i = 0;
                    }, 500);
                    if (i > 1) {
                        $(Table.config.editonebtn, element).trigger("click");
                        i = 0;
                    }
                });
            }


            // 为表格绑定事件
            Table.api.bindevent(table);

            $(document).on("click", ".btn-select", function () {
                //console.log($(document).find('.btn-select')[0].getAttribute('data-unique-id'))
                Fast.api.open("goods/product/select?shelves_id=" + attr, __('Choose Product'),{
                    callback: function (data) {
                        Fast.api.ajax("shelvesproduct/select_product?sid="+data.shelves_id+"&pids="+data.url,function (data) {
                            table.bootstrapTable('refresh');
                        })
                    }
                });

            });
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {//渲染的方法
                status: function (value, row, index) {

                    var color = '';
                    var status_text = '';
                    if(row.status == 0){
                        color = 'info';
                        status_text = __('Not reviewed');
                    }
                    if(row.status == 1){
                        color = 'success';
                        status_text = __('Online');
                    }
                    if(row.status == 2){
                        color = 'danger'
                        status_text = __('Downline');
                    }
                    //渲染状态
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i>'+status_text+'</span>';
                    return html;

                },
                pname: function (value,row,index) {
                    return '<label style="font-size: larger;">'+value+'</label>';
                }
            },
        }
    };
    return Controller;
});