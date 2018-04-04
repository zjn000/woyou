define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'statistics/productsales/index',
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: 'statistics/productsales/multi',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'p_name', title: __('P name'), operate: 'LIKE'},
                        {field: 'p_barcode', title: __('P barcode')},
                        {field: 'num', title: __('Num'), operate:false},
                        {field: 'create_time', title: __('Create time'), operate: 'BETWEEN', type: 'datetime', addclass: 'datetimepicker', data: 'data-date-format="YYYY-MM-DD HH:mm:ss"'},
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
            },

        }
    };
    return Controller;
});