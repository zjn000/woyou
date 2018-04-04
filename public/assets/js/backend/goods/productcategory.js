define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'goods/productcategory/index',
                    add_url: 'goods/productcategory/add',
                    edit_url: 'goods/productcategory/edit',
                    del_url: 'goods/productcategory/del',
                    multi_url: 'goods/productcategory/multi',
                    table: 'product_category',
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
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), operate: false,formatter: Controller.api.formatter.status},
                        {field: 'update_time', title: __('Update_time'), operate: false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                search:false
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

                }
            }
        }
    };
    return Controller;
});