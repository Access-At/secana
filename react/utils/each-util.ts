import { Children } from "react";

type Item = {};

interface Props {
  of: Item[];
  render: (item: any, index: number) => JSX.Element;
}

export default function EachUtil({ of, render }: Props) {
  return Children.toArray(of.map((item, index) => render(item, index)));
}
