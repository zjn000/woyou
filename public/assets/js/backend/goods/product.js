define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'goods/product/index',
                    add_url: 'goods/product/add',
                    edit_url: 'goods/product/edit',
                    multi_url: 'goods/product/multi',
                    table: 'product',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'name', title: __('Name'),operate: 'LIKE'},
                        {field: 'specification', title: __('Specification'), operate:false},
                        {field: 'barcode', title: __('Barcode')},
                        {field: 'type', title: __('Type'),searchList:$.getJSON("goods/product/getCategoryList")},
                        {field: 'price', title: __('Price'),operate:false},
                        {field: 'image', title: __('Image'),operate:false,formatter: Table.api.formatter.image},
                        {field: 'create_time', title: __('Create_time'),operate:false,formatter: Table.api.formatter.datetime},
                        {field: 'inventory', title: __('Inventory'),operate:false},
                        {field: 'status', title: __('Status'),searchList:{"1":__('Effective'),"0":__('Invalid')}, formatter: Controller.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons: [{
                                name: 'purchase',
                                text: __('Purchase Storage'),
                                icon: 'fa fa-shopping-cart',
                                classname: 'btn btn-info btn-xs btn-dialog',
                                url: 'goods/product/purchase'
                            },
                            {
                                name: 'returns',
                                text: __('Return Shipping'),
                                icon: 'fa fa-shopping-basket',
                                classname: 'btn btn-warning btn-xs btn-dialog',
                                url: 'goods/product/returns'
                            }],

                            formatter: Table.api.formatter.operate}
                    ]
                ],
                search: false,
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        select: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'goods/product/select',
                }
            });

            var table = $("#table");

            var attr = table.data("unique-id");
            var extend_url = '';
            if(attr != ''){
                extend_url = '/shelves_id/'+attr;
            }

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url+extend_url,
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate: false},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'barcode', title: __('Barcode')},
                        {field: 'type', title: __('Type'),searchList:$.getJSON("goods/product/getCategoryList")},
                        {field: 'image', title: __('Image'), operate: false, formatter: Table.api.formatter.image},
                        {field: 'price', title: __('Price'), operate: false},
                        {field: 'operate', title: __('Operate'), events: {
                            'click .btn-chooseone': function (e, value, row, index) {
                                var multiple = Backend.api.query('multiple');
                                multiple = multiple == 'true' ? true : false;
                                Fast.api.close({url: row.id,shelves_id:attr,multiple: false});
                            },
                        }, formatter: function () {
                            return '<a href="javascript:;" class="btn btn-danger btn-chooseone btn-xs"><i class="fa fa-check"></i> ' + __('Choose') + '</a>';
                        }}
                    ]
                ],
                search:false
            });

            // 选中多个
            $(document).on("click", ".btn-choose-multi", function () {
                var urlArr = new Array();
                $.each(table.bootstrapTable("getAllSelections"), function (i, j) {
                    urlArr.push(j.id);
                });
                var multiple = Backend.api.query('multiple');
                multiple = multiple == 'true' ? true : false;
                Fast.api.close({url: urlArr.join(","),shelves_id:attr, multiple: true});
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        purchase: function () {
            Controller.api.bindevent();
        },
        returns: function () {
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
                    if(row.status == 1){
                        color = 'success';
                        status_text = __('Effective');
                    }
                    if(row.status == 0){
                        color = 'danger'
                        status_text = __('Invalid');
                    }
                    //渲染状态
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i>'+status_text+'</span>';
                    return html;

                },
            }
        }
    };
    return Controller;
});