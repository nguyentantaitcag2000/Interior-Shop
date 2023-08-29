
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
export interface product{
    ID_Product :number,
    Name_Product:string,
    Description:string,
    Price:number,
    Avatar:string,
    Size:string,
    Amount_Product:number,
    detail_product_image: detailProductImage[]
    detail_product_material: detailProductMaterial[]
    detail_product_color: detailProductColor[]
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