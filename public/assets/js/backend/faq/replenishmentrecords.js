define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'faq/replenishmentrecords/index',
                    add_url: 'faq/replenishmentrecords/add',
                    edit_url: 'faq/replenishmentrecords/edit',
                    del_url: 'faq/replenishmentrecords/del',
                    multi_url: 'faq/replenishmentrecords/multi',
                    table: 'replenishment_records',
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
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'sid', title: __('Sid')},
                        {field: 's_name', title: __('S_name'),operate:'LIKE'},
                        {field: 'p_name', title: __('P_name'),operate:'LIKE'},
                        {field: 'p_barcode', title: __('P_barcode')},
                        {field: 'before_remaining', title: __('Before_remaining'),operate:false},
                        {field: 'real_amount', title: __('Real_amount'),operate:false},
                        {field: 'loss', title: __('Loss'),operate:false},
                        {field: 'add_num', title: __('Add_num'),operate:false},
                        {field: 'revise', title: __('Revise'),operate:false},
                        {field: 'after_remaining', title: __('After_remaining'),operate:false},
                        {field: 'admin_id', title: __('Admin_id'),searchList: $.getJSON("Ajax/ajax_admin_name")},
                        {field: 'create_time', title: __('Create_time'),formatter: Table.api.formatter.datetime, operate: 'BETWEEN', type: 'datetime', addclass: 'datetimepicker', data: 'data-date-format="YYYY-MM-DD HH:mm:ss"'},
                    ]
                ],
                search:false
            });

            //当表格数据加载完成时
            table.on('load-success.bs.table', function (e, data) {
                //这里可以获取从服务端获取的JSON数据
                // csv导出按钮事件
                $('#toolbar').on('click', '.btn-csv', function () {
                    window.location.href=data.export_url;
                });
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
            }
        }
    };
    return Controller;
});