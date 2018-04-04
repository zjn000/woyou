define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'statistics/shelvestogoods/index',
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: 'statistics/shelvestogoods/multi',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'sid', title: __('S id')},
                        {field: 'shelves.name', title: __('S name'),operate: 'LIKE'},
                        {field: 'p_name', title: __('P name'), operate: 'LIKE'},
                        {field: 'p_barcode', title: __('P barcode')},
                        {field: 'num', title: __('Num'), operate:false},
                        {field: 'total', title: __('Total'), operate:false},
                        {field: 'create_time', title: __('Create time'), operate: 'BETWEEN', type: 'datetime', addclass: 'datetimepicker', data: 'data-date-format="YYYY-MM-DD HH:mm:ss"'},
                    ]
                ],
                search: false,
            });


            //当表格数据加载完成时
            table.on('load-success.bs.table', function (e, data) {
                //这里可以获取从服务端获取的JSON数据
                $("#two").html('');
                $("#two").append("&nbsp;当页商品销售总数:<label style='color: red;'>"+data.pageNum+"</label>&emsp;当页商品销售总额:<label style='color: red;'>"+data.pageTotal+"</label>&nbsp;");
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