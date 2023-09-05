
export interface material{
    ID_Material:number,
    Name_Material: string
}
export interface color{
    ID_Color:number,
    Name_Color: string,
}
export interface category{
    ID_Category: number,
    Name_Category: string,
    Icon:string
}
export interface methodOfPayment{
    ID_MOP:number,
    Name_MOP:string
}
export interface shipMethod{
    ID_SM:number,
    Name_SM:string
}
export interface shoppingCart{
    ID_User:number,
    ID_SC:number,
    ID_CS:number,
    cart_detail:cartDetail[]
}
export interface users{
    "ID_User": number,
    "Name_User": string,
    "Phone": string,
    "Email": string,
    "Address": string,
    "verify": string,
    "regtime": number,
    "ID_UT": number
}
export interface bill_status{
    "Name_BS": string,
}
export interface bill{
    "ID_Bill": number,
    "CreateDate": string,
    "TotalMoney": number,
    "VAT_rate": number,
    "VAT_amount": number,
    "TotalMoneyCheckout": number,
    "ID_BS": number,
    "ID_Order": number,
    bill_status: bill_status
}
export interface billFull{
    "ID_Bill": number,
    "CreateDate": string,
    "TotalMoney": number,
    "VAT_rate": number,
    "VAT_amount": number,
    "TotalMoneyCheckout": number,
    "ID_BS": number,
    "ID_Order": number,
    "order": order
}
export interface cartDetail{
    ID_SC:number,
    ID_Product: number,
    Amount_CD:number,
    product: product,
    ID_Color:number,
    color:color,
    material:material,
    dimensions:dimensions,

}
export interface dimensions{
    ID_D:number,
    Name_D:string,
    ID_Product:number,
}
export interface product{
    ID_Product :number,
    Name_Product:string,
    Description:string,
    Price:number,
    Avatar:string,
    Amount_Product:number,
    category: category,
    dimensions:dimensions[],
    detail_product_image: detailProductImage[]
    detail_product_material: detailProductMaterial[]
    detail_product_color: detailProductColor[]
}
export interface orderDetail{
    ID_Order:number,
    ID_SC:number,
    shopping_cart:shoppingCart
}
export interface methodofpayment{
    ID_MOP:number,
    Name_MOP:string
}
export interface order{
    Address_O:string,
    Customer_Name:string,
    ID_MOP:number,
    ID_Order:number,
    ID_SM:number,
    Note_O:string,
    Phone_O:string,
    bill:bill,
    order_detail: orderDetail[],
    methodofpayment:methodOfPayment
    
}
export interface detailProductColor {
    ID_Product: number,
    ID_Color: number,
    color: color
}
export interface detailProductMaterial{
    ID_Product: number,
    ID_Material: number,
    material: material
}
export interface detailProductImage{
    ID_Product:number|null,
    Image:string
}
export interface importHistoryDetail{
    ID_Product:number,
    ID_IH:number,
    Price:number,
    Amount:number
}