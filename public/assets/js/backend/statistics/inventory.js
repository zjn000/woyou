define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'statistics/inventory/index',
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: 'statistics/inventory/multi',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'id', title: __('ID'),operate:false},
                        {field: 'name', title: __('Name'),operate:'LIKE'},
                        {field: 'barcode', title: __('Barcode')},
                        {field: 'type', title: __('Type'),operate:false},
                        {field: 'standard_number', title: __('Standard number'), operate:false},
                        {field: 'num', title: __('Num'), operate:false},
                        {field: 'inventory', title: __('Inventory'), operate:false},
                        {field: 'total', title: __('Total'), operate:false}
                    ]
                ],
                search: false,

            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});